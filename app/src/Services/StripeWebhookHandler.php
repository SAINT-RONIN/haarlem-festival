<?php

declare(strict_types=1);

namespace App\Services;

use App\Checkout\Interfaces\IOrderCapacityRestorer;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\CheckoutException;
use App\Exceptions\StripeWebhookException;
use App\Infrastructure\Interfaces\IStripeService;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use App\DTOs\Checkout\WebhookHandlerResult;
use App\Services\Interfaces\IStripeWebhookHandler;
use PDO;

/**
 * Processes incoming Stripe webhook events and transitions order/payment statuses.
 *
 * Extracted from CheckoutService to reduce its dependency count and isolate
 * webhook-specific logic (signature verification, idempotency, status transitions).
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
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Processes an incoming Stripe webhook event.
     *
     * @throws CheckoutException When the event payload is invalid or malformed
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): WebhookHandlerResult
    {
        $event = $this->loadWebhookEvent($payload, $signatureHeader);
        $eventId = (string)($event['id'] ?? '');
        $eventType = (string)($event['type'] ?? '');

        $this->validateWebhookEvent($eventId, $eventType);

        if (!$this->webhookEventRepository->markProcessedIfNew($eventId, $eventType)) {
            return new WebhookHandlerResult(processed: false, eventId: $eventId, eventType: $eventType);
        }

        $object = $this->extractWebhookObject($event);
        [$metadata, $orderId, $paymentId] = $this->extractWebhookMetadata($object);
        $this->processWebhookTransaction($eventType, $object, $metadata, $orderId, $paymentId);

        return new WebhookHandlerResult(processed: true, eventId: $eventId, eventType: $eventType);
    }

    /**
     * @return array<string,mixed>
     */
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

    /** Validates that a webhook event has required fields. */
    private function validateWebhookEvent(string $eventId, string $eventType): void
    {
        if ($eventId === '' || $eventType === '') {
            throw new StripeWebhookException('Invalid Stripe event payload.');
        }
    }

    /** Extracts the event object from a Stripe webhook event. */
    private function extractWebhookObject(array $event): array
    {
        $object = $event['data']['object'] ?? null;
        if (!is_array($object)) {
            throw new StripeWebhookException('Stripe event object is missing.');
        }
        return $object;
    }

    /**
     * Extracts order/payment metadata from the Stripe webhook object.
     *
     * @return array{0: array, 1: ?int, 2: ?int}
     */
    private function extractWebhookMetadata(array $object): array
    {
        $metadata = isset($object['metadata']) && is_array($object['metadata']) ? $object['metadata'] : [];
        $orderId = isset($metadata['order_id']) ? (int)$metadata['order_id'] : null;
        $paymentId = isset($metadata['payment_id']) ? (int)$metadata['payment_id'] : null;

        return [$metadata, $orderId, $paymentId];
    }

    /** Wraps webhook status transitions in a database transaction. */
    private function processWebhookTransaction(string $eventType, array $object, array $metadata, ?int $orderId, ?int $paymentId): void
    {
        $this->pdo->beginTransaction();

        try {
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

    /** Links the Stripe payment intent ID to the payment record if present. */
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

    /** Routes the webhook event type to the appropriate status transition handler. */
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

    /** Marks order as paid, payment as paid, and flags the program as checked out. */
    private function processPaymentCompleted(array $metadata, ?int $orderId, ?int $paymentId): void
    {
        if ($orderId !== null) {
            $this->orderRepository->updateStatus($orderId, OrderStatus::Paid);
        }
        if ($paymentId !== null) {
            $this->paymentRepository->updateStatus($paymentId, PaymentStatus::Paid, new \DateTimeImmutable());
        }
        $programId = isset($metadata['program_id']) ? (int)$metadata['program_id'] : 0;
        if ($programId > 0) {
            $this->programRepository->markCheckedOut($programId);
        }
    }

    /** Restores capacity and marks order as expired / payment as failed. */
    private function processPaymentFailed(?int $orderId, ?int $paymentId): void
    {
        $this->expireOrderIfPresent($orderId);
        $this->failPaymentIfPresent($paymentId);
    }

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
}
