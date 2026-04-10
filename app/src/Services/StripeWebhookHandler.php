<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\CheckoutException;
use App\Exceptions\StripeWebhookException;
use App\Infrastructure\Interfaces\IStripeService;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use App\Repositories\Interfaces\IWebhookOrderRepository;
use App\DTOs\Domain\Checkout\WebhookHandlerResult;
use App\Services\Interfaces\IStripeWebhookHandler;
use App\Services\Interfaces\IInvoiceFulfillmentService;
use App\Services\Interfaces\ITicketFulfillmentService;

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
        private readonly IWebhookOrderRepository $webhookOrderRepository,
        private readonly ITicketFulfillmentService $ticketFulfillmentService,
        private readonly IInvoiceFulfillmentService $invoiceFulfillmentService,
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
            $this->applyWebhookStatusTransition($eventType, $object, $metadata, $orderId, $paymentId);
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

    private function applyWebhookStatusTransition(string $eventType, array $object, array $metadata, ?int $orderId, ?int $paymentId): void
    {
        switch ($eventType) {
            case 'checkout.session.completed':
            case 'checkout.session.async_payment_succeeded':
                $this->processPaymentCompleted($object, $metadata, $orderId, $paymentId);
                break;

            case 'checkout.session.expired':
            case 'checkout.session.async_payment_failed':
                $this->processPaymentFailed($orderId, $paymentId);
                break;
        }
    }

    private function processPaymentCompleted(array $object, array $metadata, ?int $orderId, ?int $paymentId): void
    {
        if ($orderId === null || $paymentId === null) {
            return;
        }

        $paymentIntentId = $object['payment_intent'] ?? '';
        if (!is_string($paymentIntentId) || $paymentIntentId === '') {
            return;
        }

        $programId = isset($metadata['program_id']) ? (int) $metadata['program_id'] : 0;

        $this->webhookOrderRepository->completePayment(
            orderId: $orderId,
            paymentId: $paymentId,
            paymentIntentId: $paymentIntentId,
            programId: $programId,
            paidAtUtc: new \DateTimeImmutable(),
            allowedOrderStatuses: [OrderStatus::Pending],
            allowedPaymentStatuses: [PaymentStatus::Pending],
        );
    }

    private function processPaymentFailed(?int $orderId, ?int $paymentId): void
    {
        if ($orderId === null || $paymentId === null) {
            return;
        }

        $this->webhookOrderRepository->failPayment(
            orderId: $orderId,
            paymentId: $paymentId,
            allowedOrderStatuses: [OrderStatus::Pending],
            allowedPaymentStatuses: [PaymentStatus::Pending],
        );
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
