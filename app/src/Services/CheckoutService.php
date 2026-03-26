<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CheckoutConstraints;
use App\Enums\OrderStatus;
use App\Mappers\CheckoutMapper;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Interfaces\IStripeService;
use App\DTOs\Program\ProgramData;
use App\DTOs\Program\ProgramItemData;
use App\Models\CheckoutMainContent;
use App\Repositories\CheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\DTOs\Checkout\WebhookHandlerResult;
use App\Exceptions\CheckoutException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use PDO;

/**
 * Orchestrates the full checkout lifecycle for festival ticket orders.
 *
 * Coordinates between the program (cart), order/payment persistence, and Stripe
 * to create checkout sessions, handle cancellations, and process webhook callbacks.
 * All multi-step mutations are wrapped in database transactions.
 */
class CheckoutService implements ICheckoutService
{
    public function __construct(
        private readonly IProgramRepository $programRepository,
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IPaymentRepository $paymentRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly IStripeWebhookEventRepository $webhookEventRepository,
        private readonly IStripeService $stripeService,
        private readonly ICheckoutRuntimeConfig $runtimeConfig,
        private readonly PDO $pdo,
        private readonly CheckoutContentRepository $checkoutContentRepository,
    ) {
    }

    /**
     * Validates the payload, persists an order with line items, creates a Stripe
     * checkout session, and returns the redirect URL for the payment gateway.
     *
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails?:bool} $payload
     * @throws CheckoutException When the program is empty or Stripe session creation fails
     * @throws \InvalidArgumentException When required payload fields are missing or invalid
     */
    public function createCheckoutSession(ProgramData $programData, int $userId, array $payload): CheckoutSessionResult
    {
        $this->validatePayload($payload);
        $this->validateProgramNotEmpty($programData);
        $this->validatePositiveTotal($programData->total);
        $this->validateItemAvailability($programData->items);

        $method = $this->mapPaymentMethod((string)$payload['paymentMethod']);

        return $this->executeCheckoutTransaction($programData, $userId, $payload, $method);
    }

    /** Validates that the order total is greater than zero. */
    private function validatePositiveTotal(float $total): void
    {
        if ($total <= 0) {
            throw new CheckoutException('Total amount must be greater than zero.');
        }
    }

    /** Persists order, reserves seats, creates Stripe session inside a transaction. */
    private function executeCheckoutTransaction(ProgramData $programData, int $userId, array $payload, PaymentMethod $method): CheckoutSessionResult
    {
        $this->pdo->beginTransaction();

        try {
            $orderNumber = $this->generateOrderNumber();
            $orderId = $this->persistOrder($programData, $userId, $orderNumber);
            $this->persistOrderLineItems($programData->items, $orderId);
            $this->reserveSessionSeats($programData->items);

            $paymentId = $this->paymentRepository->create($orderId, $method, PaymentStatus::Pending);
            $checkoutUrl = $this->createAndLinkStripeSession($programData, $userId, $payload, $orderId, $paymentId, $orderNumber, $method);

            $this->pdo->commit();

            return new CheckoutSessionResult(redirectUrl: $checkoutUrl, orderId: $orderId, paymentId: $paymentId);
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Validates that the program has items before checkout. */
    private function validateProgramNotEmpty(ProgramData $programData): void
    {
        if ($programData->program === null || $programData->items === []) {
            throw new CheckoutException('Your program is empty.');
        }
    }

    /** Persists the order header with a unique order number. */
    private function persistOrder(ProgramData $programData, int $userId, string $orderNumber): int
    {
        return $this->orderRepository->create(
            userAccountId: $userId,
            programId: $programData->program->programId,
            orderNumber: $orderNumber,
            subtotal: $this->toMoneyString($programData->subtotal),
            vatTotal: $this->toMoneyString($programData->taxAmount),
            totalAmount: $this->toMoneyString($programData->total),
            payBeforeUtc: new \DateTimeImmutable('+24 hours'),
        );
    }

    /**
     * Persists each program item as an order line item.
     *
     * @param ProgramItemData[] $items
     */
    private function persistOrderLineItems(array $items, int $orderId): void
    {
        foreach ($items as $item) {
            $this->orderItemRepository->create(
                orderId: $orderId,
                eventSessionId: $item->eventSessionId,
                historyTourId: null,
                passPurchaseId: null,
                quantity: $item->quantity,
                unitPrice: $this->toMoneyString($item->basePrice),
                vatRate: $this->toMoneyString($this->runtimeConfig->getVatRate() * 100),
                donationAmount: $this->toMoneyString($item->donationAmount),
            );
        }
    }

    /**
     * Atomically reserves seats for all items — prevents overselling under concurrent checkouts.
     *
     * @param ProgramItemData[] $items
     */
    private function reserveSessionSeats(array $items): void
    {
        foreach ($items as $item) {
            if ($item->eventSessionId <= 0) {
                continue;
            }

            $reserved = $this->eventSessionRepository->decrementCapacity($item->eventSessionId, $item->quantity);

            if (!$reserved) {
                throw new CheckoutException(
                    "Seats no longer available for '{$item->eventTitle}'. Please update your program."
                );
            }
        }
    }

    /**
     * Creates a Stripe checkout session, links its identifiers to the payment record,
     * and returns the redirect URL.
     *
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails?:bool} $payload
     */
    private function createAndLinkStripeSession(
        ProgramData $programData,
        int $userId,
        array $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): string {
        $appUrl = $this->runtimeConfig->getAppUrl();

        $session = $this->stripeService->createCheckoutSession([
            'mode' => 'payment',
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
            'payment_method_types' => $this->mapStripePaymentMethodTypes($method),
            'line_items' => CheckoutMapper::buildStripeLineItems($programData->total, $orderNumber),
            'customer_email' => (string)$payload['email'],
            'client_reference_id' => $orderNumber,
            'metadata' => [
                'order_id' => (string)$orderId,
                'payment_id' => (string)$paymentId,
                'program_id' => (string)$programData->program->programId,
                'user_id' => (string)$userId,
                'first_name' => (string)$payload['firstName'],
                'last_name' => (string)$payload['lastName'],
            ],
        ]);

        $sessionId = (string)($session['id'] ?? '');
        $checkoutUrl = (string)($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutException('Stripe checkout session could not be created.');
        }

        $this->linkStripeIds($paymentId, $session, $sessionId);

        return $checkoutUrl;
    }

    /** Links Stripe session and payment intent identifiers back to the payment record. */
    private function linkStripeIds(int $paymentId, array $session, string $sessionId): void
    {
        $this->paymentRepository->updateStripeSessionId($paymentId, $sessionId);
        $this->paymentRepository->updateProviderRef($paymentId, $sessionId);

        $paymentIntentId = $session['payment_intent'] ?? null;
        if (is_string($paymentIntentId) && $paymentIntentId !== '') {
            $this->paymentRepository->updateStripePaymentIntentId($paymentId, $paymentIntentId);
        }
    }

    /**
     * Marks a pending order and its payment as cancelled when the user abandons checkout.
     * Only transitions records that are still in Pending status to avoid overwriting webhook updates.
     *
     */
    public function handleCancel(?int $orderId, ?int $paymentId): CheckoutCancelResult
    {
        if ($orderId !== null) {
            // Restore reserved capacity for each session in the cancelled order
            $this->restoreOrderCapacity($orderId);
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
        }

        if ($paymentId !== null) {
            $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
        }

        return new CheckoutCancelResult(
            status: 'cancelled',
            orderId: $orderId,
            paymentId: $paymentId,
        );
    }

    /**
     * Processes an incoming Stripe webhook event. Verifies the signature, ensures
     * idempotency via the webhook event repository, and transitions order/payment
     * statuses based on the event type (completed, expired, or failed).
     *
     * @throws CheckoutException When the event payload is invalid or malformed
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): WebhookHandlerResult
    {
        $event = $this->stripeService->constructWebhookEvent($payload, $signatureHeader);
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

    /** Validates that a webhook event has required fields. */
    private function validateWebhookEvent(string $eventId, string $eventType): void
    {
        if ($eventId === '' || $eventType === '') {
            throw new CheckoutException('Invalid Stripe event payload.');
        }
    }

    /** Extracts the event object from a Stripe webhook event. */
    private function extractWebhookObject(array $event): array
    {
        $object = $event['data']['object'] ?? null;
        if (!is_array($object)) {
            throw new CheckoutException('Stripe event object is missing.');
        }
        return $object;
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
        if ($orderId !== null) {
            $this->restoreOrderCapacity($orderId);
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Expired, [OrderStatus::Pending]);
        }
        if ($paymentId !== null) {
            $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Failed, [PaymentStatus::Pending]);
        }
    }

    /**
     * Retrieves a Stripe checkout session and returns a normalized summary
     * used to display the order confirmation or failure page.
     *
     * @throws \InvalidArgumentException When sessionId is empty
     */
    public function getSessionSummary(string $sessionId): CheckoutSessionSummary
    {
        if ($sessionId === '') {
            throw new \InvalidArgumentException('Missing Stripe session id.');
        }

        $session = $this->stripeService->retrieveCheckoutSession($sessionId);

        return new CheckoutSessionSummary(
            sessionId: $session['id'] ?? '',
            paymentStatus: $session['payment_status'] ?? 'unpaid',
            status: $session['status'] ?? 'open',
            amountTotal: isset($session['amount_total']) ? ((int)$session['amount_total'] / 100) : 0,
            currency: strtoupper((string)($session['currency'] ?? 'eur')),
        );
    }

    /**
     * @param array<string,mixed> $payload
     */
    private function validatePayload(array $payload): void
    {
        foreach (['firstName', 'lastName', 'email', 'paymentMethod'] as $requiredKey) {
            $value = trim((string)($payload[$requiredKey] ?? ''));
            if ($value === '') {
                throw new \InvalidArgumentException($requiredKey . ' is required.');
            }
        }

        if (!filter_var((string)$payload['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid email address.');
        }

        $this->mapPaymentMethod((string)$payload['paymentMethod']);
    }

    private function mapPaymentMethod(string $rawMethod): PaymentMethod
    {
        return match ($rawMethod) {
            'credit_card' => PaymentMethod::CreditCard,
            'ideal' => PaymentMethod::Ideal,
            default => throw new \InvalidArgumentException('Unsupported payment method.'),
        };
    }

    /**
     * @return string[]
     */
    private function mapStripePaymentMethodTypes(PaymentMethod $method): array
    {
        return match ($method) {
            PaymentMethod::CreditCard => ['card'],
            PaymentMethod::Ideal => ['ideal'],
            default => ['card'],
        };
    }

    /**
     * Validates that every session in the cart has enough capacity.
     * Enforces sold-out prevention, quantity limits, and the 90% single-ticket cap.
     *
     * @param ProgramItemData[] $items
     * @throws CheckoutException When any session lacks sufficient capacity
     */
    private function validateItemAvailability(array $items): void
    {
        foreach ($items as $item) {
            if ($item->eventSessionId <= 0) {
                continue;
            }
            $this->validateSingleItemAvailability($item);
        }
    }

    /** Validates capacity, quantity, and single-ticket policy for one cart item. */
    private function validateSingleItemAvailability(ProgramItemData $item): void
    {
        $capacity = $this->eventSessionRepository->getCapacityInfo($item->eventSessionId);

        if ($capacity === null) {
            throw new CheckoutException("Session for '{$item->eventTitle}' no longer exists.");
        }

        $available = $capacity->getAvailableSeats();
        $this->validateSeatAvailability($item, $available);
        $this->validateSingleTicketCap($item, $capacity);
    }

    /** Ensures the session has enough seats for the requested quantity. */
    private function validateSeatAvailability(ProgramItemData $item, int $available): void
    {
        if ($available <= 0) {
            throw new CheckoutException("'{$item->eventTitle}' is sold out.");
        }

        if ($item->quantity > $available) {
            throw new CheckoutException(
                "Only {$available} seats remaining for '{$item->eventTitle}'. You requested {$item->quantity}."
            );
        }
    }

    /** Enforces the 90% single-ticket cap (10% reserved for pass holders). */
    private function validateSingleTicketCap(ProgramItemData $item, object $capacity): void
    {
        $singleTicketCap = (int)floor($capacity->capacityTotal * CheckoutConstraints::SINGLE_TICKET_CAPACITY_RATIO);

        if (($capacity->soldSingleTickets + $item->quantity) <= $singleTicketCap) {
            return;
        }

        $remaining = max(0, $singleTicketCap - $capacity->soldSingleTickets);
        throw new CheckoutException(
            "Single-ticket limit reached for '{$item->eventTitle}'. "
            . ($remaining > 0 ? "Only {$remaining} single tickets remaining." : 'Passes may still be available.')
        );
    }

    /**
     * Restores reserved capacity for all sessions in an order.
     * Called when an order is cancelled or a payment fails/expires.
     */
    private function restoreOrderCapacity(int $orderId): void
    {
        $orderItems = $this->orderItemRepository->findByOrderId($orderId);

        foreach ($orderItems as $item) {
            if ($item->eventSessionId !== null && $item->eventSessionId > 0) {
                $this->eventSessionRepository->restoreCapacity($item->eventSessionId, $item->quantity);
            }
        }
    }

    private function toMoneyString(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    private function generateOrderNumber(): string
    {
        return 'HF-' . gmdate('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /**
     * Returns the CMS content for the checkout page.
     */
    public function getCheckoutMainContent(): CheckoutMainContent
    {
        return $this->checkoutContentRepository->findCheckoutMainContent('checkout', 'main');
    }
}

