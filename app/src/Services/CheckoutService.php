<?php

declare(strict_types=1);

namespace App\Services;

use App\Checkout\Interfaces\IOrderCapacityRestorer;
use App\Constants\CheckoutConstraints;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Interfaces\IStripeService;
use App\DTOs\Program\ProgramData;
use App\DTOs\Program\ProgramItemData;
use App\Content\CheckoutMainContent;
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPassPurchaseRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\Exceptions\CheckoutException;
use App\Exceptions\CheckoutInputException;
use App\Exceptions\CheckoutSessionException;
use App\Exceptions\RetryPaymentException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use App\Services\Interfaces\ITicketFulfillmentService;
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
        private readonly IOrderCapacityRestorer $orderCapacityRestorer,
        private readonly ITicketFulfillmentService $ticketFulfillmentService,
        private readonly IPassPurchaseRepository $passPurchaseRepository,
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
            throw new CheckoutInputException('Total amount must be greater than zero.');
        }
    }

    /** Persists order, reserves seats, creates Stripe session inside a transaction. */
    private function executeCheckoutTransaction(ProgramData $programData, int $userId, array $payload, PaymentMethod $method): CheckoutSessionResult
    {
        $this->pdo->beginTransaction();

        try {
            $orderNumber = $this->generateOrderNumber();
            $orderId = $this->persistOrder($programData, $userId, $orderNumber, $payload);
            $this->persistOrderLineItems($programData->items, $orderId, $userId);
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
            throw new CheckoutInputException('Your program is empty.');
        }
    }

    /** Persists the order header with a unique order number. */
    private function persistOrder(ProgramData $programData, int $userId, string $orderNumber, array $payload): int
    {
        return $this->orderRepository->create(
            userAccountId: $userId,
            programId: $programData->program->programId,
            orderNumber: $orderNumber,
            subtotal: $this->toMoneyString($programData->subtotal),
            vatTotal: $this->toMoneyString($programData->taxAmount),
            totalAmount: $this->toMoneyString($programData->total),
            ticketRecipientFirstName: trim((string)$payload['firstName']),
            ticketRecipientLastName: trim((string)$payload['lastName']),
            ticketRecipientEmail: trim((string)$payload['email']),
            payBeforeUtc: new \DateTimeImmutable('+24 hours'),
        );
    }

    /**
     * Persists each program item as an order line item.
     *
     * @param ProgramItemData[] $items
     */
    private function persistOrderLineItems(array $items, int $orderId, int $userId): void
    {
        foreach ($items as $item) {
            if ($item->passTypeId !== null) {
                $this->persistPassOrderItem($item, $orderId, $userId);
                continue;
            }

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

    /** Creates a PassPurchase record and links it to an order line item. */
    private function persistPassOrderItem(ProgramItemData $item, int $orderId, int $userId): void
    {
        $passPurchaseId = $this->passPurchaseRepository->create(
            passTypeId: $item->passTypeId,
            userAccountId: $userId,
            validDate: $item->passValidDate,
            validFromDate: null,
            validToDate: null,
        );

        $this->orderItemRepository->create(
            orderId: $orderId,
            eventSessionId: null,
            historyTourId: null,
            passPurchaseId: $passPurchaseId,
            quantity: $item->quantity,
            unitPrice: $this->toMoneyString($item->basePrice),
            vatRate: $this->toMoneyString($this->runtimeConfig->getVatRate() * 100),
            donationAmount: $this->toMoneyString($item->donationAmount),
        );
    }

    /**
     * Atomically reserves seats for all items — prevents overselling under concurrent checkouts.
     *
     * @param ProgramItemData[] $items
     */
    private function reserveSessionSeats(array $items): void
    {
        foreach ($items as $item) {
            if ($item->eventSessionId === null || $item->eventSessionId <= 0) {
                continue;
            }

            $reserved = $this->eventSessionRepository->decrementCapacity($item->eventSessionId, $item->quantity);

            if (!$reserved) {
                throw new CheckoutInputException(
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
        $session = $this->createStripeSession($programData, $userId, $payload, $orderId, $paymentId, $orderNumber, $method);

        $sessionId = (string)($session['id'] ?? '');
        $checkoutUrl = (string)($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutSessionException('Stripe checkout session could not be created.');
        }

        $this->linkStripeIds($paymentId, $session, $sessionId);

        return $checkoutUrl;
    }

    /**
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails?:bool} $payload
     * @return array<string,mixed>
     */
    private function createStripeSession(
        ProgramData $programData,
        int $userId,
        array $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): array {
        try {
            return $this->stripeService->createCheckoutSession(
                $this->buildStripeSessionParams($programData, $userId, $payload, $orderId, $paymentId, $orderNumber, $method),
            );
        } catch (\Throwable $error) {
            throw new CheckoutSessionException('Stripe checkout session could not be created.', 0, $error);
        }
    }

    /**
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails?:bool} $payload
     * @return array<string,mixed>
     */
    private function buildStripeSessionParams(
        ProgramData $programData,
        int $userId,
        array $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): array {
        $appUrl = $this->runtimeConfig->getAppUrl();

        return [
            'mode' => 'payment',
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
            'payment_method_types' => $this->mapStripePaymentMethodTypes($method),
            'line_items' => $this->buildStripeLineItems($programData->total, $orderNumber),
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
        ];
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
     * Loads and validates an order for retry payment.
     */
    public function getRetryOrder(int $orderId, int $userId): \App\Models\Order
    {
        $order = $this->orderRepository->findByIdAndUserId($orderId, $userId);

        if ($order === null) {
            throw new RetryPaymentException('Order not found.');
        }

        return $order;
    }

    /**
     * Creates a new Stripe session for an existing pending order within the 24h window.
     */
    public function retryCheckoutSession(int $orderId, int $userId, array $payload): CheckoutSessionResult
    {
        $order = $this->getRetryOrder($orderId, $userId);
        $this->validateRetryEligibility($order);

        $method = $this->mapPaymentMethod((string) ($payload['paymentMethod'] ?? ''));

        return $this->executeRetryTransaction($order, $method);
    }

    private function validateRetryEligibility(\App\Models\Order $order): void
    {
        if ($order->status !== OrderStatus::Pending) {
            throw new RetryPaymentException('This order is no longer pending.');
        }

        if ($order->payBeforeUtc !== null && $order->payBeforeUtc < new \DateTimeImmutable('now')) {
            throw new RetryPaymentException('The payment deadline has passed.');
        }
    }

    private function executeRetryTransaction(\App\Models\Order $order, PaymentMethod $method): CheckoutSessionResult
    {
        $this->pdo->beginTransaction();

        try {
            $paymentId = $this->paymentRepository->create($order->orderId, $method, PaymentStatus::Pending);
            $checkoutUrl = $this->createAndLinkRetryStripeSession($order, $paymentId, $method);

            $this->pdo->commit();

            return new CheckoutSessionResult(
                redirectUrl: $checkoutUrl,
                orderId: $order->orderId,
                paymentId: $paymentId,
            );
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    private function createAndLinkRetryStripeSession(\App\Models\Order $order, int $paymentId, PaymentMethod $method): string
    {
        $params = $this->buildRetryStripeParams($order, $paymentId, $method);
        $session = $this->stripeService->createCheckoutSession($params);

        $sessionId = (string) ($session['id'] ?? '');
        $checkoutUrl = (string) ($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutSessionException('Stripe checkout session could not be created.');
        }

        $this->linkStripeIds($paymentId, $session, $sessionId);

        return $checkoutUrl;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRetryStripeParams(\App\Models\Order $order, int $paymentId, PaymentMethod $method): array
    {
        $appUrl = $this->runtimeConfig->getAppUrl();

        return [
            'mode' => 'payment',
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $order->orderId . '&payment_id=' . $paymentId,
            'payment_method_types' => $this->mapStripePaymentMethodTypes($method),
            'line_items' => $this->buildStripeLineItems((float) $order->totalAmount, $order->orderNumber),
            'customer_email' => $order->ticketRecipientEmail ?? '',
            'client_reference_id' => $order->orderNumber,
            'metadata' => [
                'order_id' => (string) $order->orderId,
                'payment_id' => (string) $paymentId,
                'program_id' => (string) $order->programId,
                'user_id' => (string) $order->userAccountId,
                'first_name' => (string) ($order->ticketRecipientFirstName ?? ''),
                'last_name' => (string) ($order->ticketRecipientLastName ?? ''),
            ],
        ];
    }

    /**
     * Marks a pending order and its payment as cancelled when the user abandons checkout.
     * Only transitions records that are still in Pending status to avoid overwriting webhook updates.
     *
     */
    public function handleCancel(?int $orderId, ?int $paymentId): CheckoutCancelResult
    {
        $this->cancelOrderIfPresent($orderId);
        $this->cancelPaymentIfPresent($paymentId);

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

        $session = $this->loadCheckoutSession($sessionId);
        $this->fulfillPaidOrderFromSession($session);

        return new CheckoutSessionSummary(
            orderReference: (string)($session['client_reference_id'] ?? ''),
            amountTotal: isset($session['amount_total']) ? ((int)$session['amount_total'] / 100) : 0,
            currency: strtoupper((string)($session['currency'] ?? 'eur')),
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function loadCheckoutSession(string $sessionId): array
    {
        try {
            return $this->stripeService->retrieveCheckoutSession($sessionId);
        } catch (\Throwable $error) {
            throw new CheckoutSessionException('Stripe checkout session could not be loaded.', 0, $error);
        }
    }

    /**
     * Uses the verified Stripe success return as a fulfillment fallback when the
     * webhook is unavailable in local/dev. The fulfillment service is idempotent,
     * so repeated success-page refreshes are safe.
     *
     * @param array<string,mixed> $session
     */
    private function fulfillPaidOrderFromSession(array $session): void
    {
        if (!$this->isPaidSession($session)) {
            return;
        }

        $orderId = $this->extractOrderIdFromSession($session);
        if ($orderId === null) {
            return;
        }

        $this->ticketFulfillmentService->fulfillPaidOrder(
            $orderId,
            $this->extractCustomerEmailFromSession($session),
            $this->extractMetadataString($session, 'first_name'),
            $this->extractMetadataString($session, 'last_name'),
        );
    }

    /**
     * @param array<string,mixed> $session
     */
    private function isPaidSession(array $session): bool
    {
        return ($session['payment_status'] ?? null) === 'paid';
    }

    /**
     * @param array<string,mixed> $session
     */
    private function extractOrderIdFromSession(array $session): ?int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['order_id'])) {
            return null;
        }

        $orderId = (int)$metadata['order_id'];
        return $orderId > 0 ? $orderId : null;
    }

    /**
     * @param array<string,mixed> $session
     */
    private function extractCustomerEmailFromSession(array $session): ?string
    {
        $customerDetails = $session['customer_details'] ?? null;
        if (is_array($customerDetails) && isset($customerDetails['email']) && is_string($customerDetails['email'])) {
            $email = trim($customerDetails['email']);
            if ($email !== '') {
                return $email;
            }
        }

        $customerEmail = $session['customer_email'] ?? null;
        if (!is_string($customerEmail)) {
            return null;
        }

        $customerEmail = trim($customerEmail);
        return $customerEmail !== '' ? $customerEmail : null;
    }

    /**
     * @param array<string,mixed> $session
     */
    private function extractMetadataString(array $session, string $key): ?string
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata[$key]) || !is_string($metadata[$key])) {
            return null;
        }

        $value = trim($metadata[$key]);
        return $value !== '' ? $value : null;
    }

    /**
     * @return array<int, array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}>
     */
    private function buildStripeLineItems(float $total, string $orderNumber): array
    {
        return [[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int)round($total * 100),
                'product_data' => ['name' => 'Haarlem Festival order ' . $orderNumber],
            ],
            'quantity' => 1,
        ]];
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
            if ($item->passTypeId !== null || $item->eventSessionId === null || $item->eventSessionId <= 0) {
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
            throw new CheckoutInputException("Session for '{$item->eventTitle}' no longer exists.");
        }

        $available = $capacity->getAvailableSeats();
        $this->validateSeatAvailability($item, $available);
        $this->validateSingleTicketCap($item, $capacity);
    }

    /** Ensures the session has enough seats for the requested quantity. */
    private function validateSeatAvailability(ProgramItemData $item, int $available): void
    {
        if ($available <= 0) {
            throw new CheckoutInputException("'{$item->eventTitle}' is sold out.");
        }

        if ($item->quantity > $available) {
            throw new CheckoutInputException(
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
        throw new CheckoutInputException(
            "Single-ticket limit reached for '{$item->eventTitle}'. "
            . ($remaining > 0 ? "Only {$remaining} single tickets remaining." : 'Passes may still be available.')
        );
    }

    private function cancelOrderIfPresent(?int $orderId): void
    {
        if ($orderId === null) {
            return;
        }

        $this->orderCapacityRestorer->restore($orderId);
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
    }

    private function cancelPaymentIfPresent(?int $paymentId): void
    {
        if ($paymentId === null) {
            return;
        }

        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
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
