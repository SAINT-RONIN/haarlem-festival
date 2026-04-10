<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IOrderCapacityRestorer;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\CheckoutException;
use App\Exceptions\StripeWebhookException;
use App\Infrastructure\Interfaces\IStripeService;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use App\DTOs\Domain\Checkout\WebhookHandlerResult;
use App\Services\Interfaces\IStripeWebhookHandler;
use App\Services\Interfaces\IInvoiceFulfillmentService;
use App\Services\Interfaces\ITicketFulfillmentService;
use PDO;

/**
 * Processes Stripe webhook callbacks and converts them into local order/payment changes.
 * Kept separate from CheckoutService so browser-driven flow and Stripe-driven background flow
 * don't mix in the same class.
 */
class StripeWebhookHandler implements IStripeWebhookHandler
{
    public function __construct(
        private readonly IStripeService $stripeService,
        private readonly IStripeWebhookEventRepository $webhookEventRepository,
        private readonly IOrderRepository $orderRepository,
        private readonly IPaymentRepository $paymentRepository,
        private readonly IProgramRepository $programRepository,
        private readonly IOrderCapacityRestorer $orderCapacityRestorer,
        private readonly ITicketFulfillmentService $ticketFulfillmentService,
        private readonly IInvoiceFulfillmentService $invoiceFulfillmentService,
        private readonly PDO $pdo,
    ) {}

    /** @throws CheckoutException When the event payload is invalid or malformed */
    public function handleWebhook(string $payload, ?string $signatureHeader): WebhookHandlerResult
    {
        $event = $this->loadWebhookEvent($payload, $signatureHeader);
        $eventId = (string) ($event['id'] ?? '');
        $eventType = (string) ($event['type'] ?? '');

        $this->validateWebhookEvent($eventId, $eventType);

        if (!$this->webhookEventRepository->markProcessedIfNew($eventId, $eventType)) {
            return new WebhookHandlerResult(processed: false, eventId: $eventId, eventType: $eventType);
        }

        try {
            $object = $this->extractWebhookObject($event);
            // Metadata carries our internal ids because Stripe only knows about its own objects.
            [$metadata, $orderId, $paymentId] = $this->extractWebhookMetadata($object);
            $this->processWebhookTransaction($eventType, $object, $metadata, $orderId, $paymentId);
            $this->fulfillTicketsIfPaymentCompleted($eventType, $object, $metadata, $orderId);
            $this->fulfillInvoiceIfPaymentCompleted($eventType, $orderId);
        } catch (\Throwable $error) {
            $this->safeReleaseWebhookReservation($eventId);
            throw $error;
        }

        return new WebhookHandlerResult(processed: true, eventId: $eventId, eventType: $eventType);
    }

    /** @return array<string,mixed> */
    private function loadWebhookEvent(string $payload, ?string $signatureHeader): array
    {
        try {
            return $this->stripeService->constructWebhookEvent($payload, $signatureHeader);
        } catch (\InvalidArgumentException $error) {
            throw new StripeWebhookException($error->getMessage(), 0, $error);
        } catch (\Throwable $error) {
            throw new StripeWebhookException('Stripe webhook could not be validated.', 0, $error);
        }
    }

    private function validateWebhookEvent(string $eventId, string $eventType): void
    {
        if ($eventId === '' || $eventType === '') {
            throw new StripeWebhookException('Invalid Stripe event payload.');
        }
    }

    private function extractWebhookObject(array $event): array
    {
        $object = $event['data']['object'] ?? null;
        if (!is_array($object)) {
            throw new StripeWebhookException('Stripe event object is missing.');
        }

        return $object;
    }

    /** @return array{0: array, 1: ?int, 2: ?int} */
    private function extractWebhookMetadata(array $object): array
    {
        $metadata = isset($object['metadata']) && is_array($object['metadata']) ? $object['metadata'] : [];
        $orderId = isset($metadata['order_id']) ? (int) $metadata['order_id'] : null;
        $paymentId = isset($metadata['payment_id']) ? (int) $metadata['payment_id'] : null;

        return [$metadata, $orderId, $paymentId];
    }

    private function processWebhookTransaction(string $eventType, array $object, array $metadata, ?int $orderId, ?int $paymentId): void
    {
        $this->pdo->beginTransaction();

        try {
            // These mutations belong together so they either all succeed or all roll back.
            $this->updatePaymentIntentIfPresent($paymentId, $object);
            $this->applyWebhookStatusTransition($eventType, $metadata, $orderId, $paymentId);
            $this->pdo->commit();
        } catch (CheckoutException|\InvalidArgumentException|\RuntimeException $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    private function updatePaymentIntentIfPresent(?int $paymentId, array $object): void
    {
        if ($paymentId === null) {
            return;
        }
        $intentId = $object['payment_intent'] ?? null;
        if (is_string($intentId) && $intentId !== '') {
            $this->paymentRepository->updateStripePaymentIntentId($paymentId, $intentId);
        }
    }

    private function applyWebhookStatusTransition(string $eventType, array $metadata, ?int $orderId, ?int $paymentId): void
    {
        switch ($eventType) {
            case 'checkout.session.completed':
            case 'checkout.session.async_payment_succeeded':
                $this->processPaymentCompleted($metadata, $orderId, $paymentId);
                break;

            case 'checkout.session.expired':
            case 'checkout.session.async_payment_failed':
                $this->processPaymentFailed($orderId, $paymentId);
                break;
        }
    }

    private function processPaymentCompleted(array $metadata, ?int $orderId, ?int $paymentId): void
    {
        if ($orderId !== null) {
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Paid, [OrderStatus::Pending]);
        }
        if ($paymentId !== null) {
            $this->paymentRepository->updateStatusIfCurrentIn(
                $paymentId,
                PaymentStatus::Paid,
                [PaymentStatus::Pending],
                new \DateTimeImmutable(),
            );
        }
        $programId = isset($metadata['program_id']) ? (int) $metadata['program_id'] : 0;
        if ($programId > 0) {
            $this->programRepository->markCheckedOut($programId);
        }
    }

    private function processPaymentFailed(?int $orderId, ?int $paymentId): void
    {
        $this->expireOrderIfPresent($orderId);
        $this->failPaymentIfPresent($paymentId);
    }

    /**
     * Restores reserved capacity and expires the order.
     * Failed payments must release their reserved seats so capacity isn't blocked indefinitely.
     */
    private function expireOrderIfPresent(?int $orderId): void
    {
        if ($orderId === null) {
            return;
        }

        $this->orderCapacityRestorer->restore($orderId);
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Expired, [OrderStatus::Pending]);
    }

    private function failPaymentIfPresent(?int $paymentId): void
    {
        if ($paymentId === null) {
            return;
        }

        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Failed, [PaymentStatus::Pending]);
    }

    private function fulfillTicketsIfPaymentCompleted(
        string $eventType,
        array $object,
        array $metadata,
        ?int $orderId,
    ): void {
        if ($orderId === null || !$this->isPaymentCompletedEvent($eventType)) {
            return;
        }

        $this->ticketFulfillmentService->fulfillPaidOrder(
            $orderId,
            $this->extractCustomerEmail($object),
            isset($metadata['first_name']) ? (string) $metadata['first_name'] : null,
            isset($metadata['last_name']) ? (string) $metadata['last_name'] : null,
        );
    }

    private function fulfillInvoiceIfPaymentCompleted(string $eventType, ?int $orderId): void
    {
        if ($orderId === null || !$this->isPaymentCompletedEvent($eventType)) {
            return;
        }

        $this->invoiceFulfillmentService->fulfillPaidOrder($orderId);
    }

    private function isPaymentCompletedEvent(string $eventType): bool
    {
        return in_array($eventType, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true);
    }

    /**
     * Prefers structured customer_details data but falls back to customer_email
     * because Stripe can provide the email in more than one place depending on the checkout flow.
     */
    private function extractCustomerEmail(array $object): ?string
    {
        $customerDetails = $object['customer_details'] ?? null;
        if (is_array($customerDetails) && isset($customerDetails['email']) && is_string($customerDetails['email'])) {
            $email = trim($customerDetails['email']);
            if ($email !== '') {
                return $email;
            }
        }

        $customerEmail = $object['customer_email'] ?? null;
        if (is_string($customerEmail)) {
            $customerEmail = trim($customerEmail);
            if ($customerEmail !== '') {
                return $customerEmail;
            }
        }

        return null;
    }

    /**
     * Releases the stored webhook reservation so a retry is allowed.
     * Logging the cleanup failure matters because a stuck reservation can silently block future retries.
     */
    private function safeReleaseWebhookReservation(string $eventId): void
    {
        try {
            $this->webhookEventRepository->release($eventId);
        } catch (\Throwable $error) {
            error_log(
                'Failed to release Stripe webhook reservation for event '
                . $eventId
                . ': '
                . $error->getMessage()
            );
        }
    }
}
