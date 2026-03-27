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
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\Exceptions\CheckoutException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use PDO;

/**
 * Orchestrates the checkout lifecycle for festival ticket orders.
 *
 * Coordinates between the program (cart), order/payment persistence, and Stripe
 * to create checkout sessions and handle cancellations.
 * Webhook processing is handled separately by StripeWebhookHandler.
 * All multi-step mutations are wrapped in database transactions.
 */
class CheckoutService implements ICheckoutService
{
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IPaymentRepository $paymentRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly IStripeService $stripeService,
        private readonly ICheckoutRuntimeConfig $runtimeConfig,
        private readonly PDO $pdo,
        private readonly ICheckoutContentRepository $checkoutContentRepository,
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

