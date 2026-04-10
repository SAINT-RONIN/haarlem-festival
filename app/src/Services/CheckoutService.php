<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IOrderCapacityRestorer;
use App\Constants\CheckoutConstraints;
use App\Enums\PriceTierId;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Interfaces\IStripeService;
use App\DTOs\Domain\Program\ProgramData;
use App\DTOs\Domain\Program\ProgramItemData;
use App\DTOs\Cms\CheckoutMainContent;
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPassPurchaseRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\DTOs\Domain\Checkout\CheckoutCancelResult;
use App\DTOs\Domain\Checkout\CheckoutPayloadData;
use App\DTOs\Domain\Checkout\CheckoutSessionResult;
use App\DTOs\Domain\Checkout\CheckoutSessionSummary;
use App\Exceptions\CheckoutException;
use App\Exceptions\CheckoutInputException;
use App\Exceptions\CheckoutSessionException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RetryPaymentException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\DTOs\Domain\Events\SessionCapacityInfo;
use PDO;

// Orchestrates the full checkout flow: validate cart, persist order, reserve capacity, redirect to Stripe.
// Webhook handling is split into StripeWebhookHandler.
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
        private readonly IProgramRepository $programRepository,
    ) {}

    /** @throws CheckoutException|\InvalidArgumentException */
    public function createCheckoutSession(ProgramData $programData, int $userId, CheckoutPayloadData $payload): CheckoutSessionResult
    {
        $this->validatePayload($payload);
        $this->validateProgramNotEmpty($programData);
        $this->validatePositiveTotal($programData->total);
        $this->validateItemAvailability($programData->items);

        $method = $this->mapPaymentMethod($payload->paymentMethod);

        return $this->executeCheckoutTransaction($programData, $userId, $payload, $method);
    }

    // Guards against zero, negative, NaN, and Infinity.
    private function validatePositiveTotal(float $total): void
    {
        if (is_nan($total)) {
            throw new CheckoutInputException('Total amount is invalid.');
        }

        if (is_infinite($total)) {
            throw new CheckoutInputException('Total amount is invalid.');
        }

        if ($total <= 0) {
            throw new CheckoutInputException('Total amount must be greater than zero.');
        }
    }

    private function executeCheckoutTransaction(ProgramData $programData, int $userId, CheckoutPayloadData $payload, PaymentMethod $method): CheckoutSessionResult
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

    private function validateProgramNotEmpty(ProgramData $programData): void
    {
        if ($programData->program === null || $programData->items === []) {
            throw new CheckoutInputException('Your program is empty.');
        }
    }

    private function persistOrder(ProgramData $programData, int $userId, string $orderNumber, CheckoutPayloadData $payload): int
    {
        return $this->orderRepository->create(
            userAccountId: $userId,
            programId: $programData->program->programId,
            orderNumber: $orderNumber,
            subtotal: $this->toMoneyString($programData->subtotal),
            vatTotal: $this->toMoneyString($programData->taxAmount),
            totalAmount: $this->toMoneyString($programData->total),
            ticketRecipientFirstName: $payload->firstName,
            ticketRecipientLastName: $payload->lastName,
            ticketRecipientEmail: $payload->email,
            payBeforeUtc: new \DateTimeImmutable('+24 hours'),
        );
    }

    /** @param ProgramItemData[] $items */
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

    // Pass products need a PassPurchase row first, then an OrderItem pointing to it.
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

    /** @param ProgramItemData[] $items */
    private function reserveSessionSeats(array $items): void
    {
        foreach ($items as $item) {
            if ($item->eventSessionId === null || $item->eventSessionId <= 0) {
                continue;
            }

            $seatCount = $this->calculateSeatCount($item->quantity, $item->priceTierId);
            $reserved = $this->eventSessionRepository->decrementCapacity($item->eventSessionId, $seatCount);

            if (!$reserved) {
                throw new CheckoutInputException(
                    "Seats no longer available for '{$item->eventTitle}'. Please update your program."
                );
            }
        }
    }

    /**
     * Converts a ticket quantity to a seat count.
     * Group tickets (Family, Group) represent GROUP_TICKET_SEAT_COUNT people each.
     */
    private function calculateSeatCount(int $quantity, ?int $priceTierId): int
    {
        if ($priceTierId !== null && in_array($priceTierId, [PriceTierId::Family->value, PriceTierId::Group->value], true)) {
            return $quantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT;
        }

        return $quantity;
    }

    private function createAndLinkStripeSession(
        ProgramData $programData,
        int $userId,
        CheckoutPayloadData $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): string {
        $session = $this->createStripeSession($programData, $userId, $payload, $orderId, $paymentId, $orderNumber, $method);

        $sessionId = (string) ($session['id'] ?? '');
        $checkoutUrl = (string) ($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutSessionException('Stripe checkout session could not be created.');
        }

        $this->linkStripeIds($paymentId, $session, $sessionId);

        return $checkoutUrl;
    }

    /**
     * @return array<string,mixed>
     */
    private function createStripeSession(
        ProgramData $programData,
        int $userId,
        CheckoutPayloadData $payload,
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
     * @return array<string,mixed>
     */
    private function buildStripeSessionParams(
        ProgramData $programData,
        int $userId,
        CheckoutPayloadData $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): array {
        return $this->buildStripeCheckoutParams(
            orderId: $orderId,
            paymentId: $paymentId,
            method: $method,
            total: $programData->total,
            orderNumber: $orderNumber,
            customerEmail: $payload->email,
            metadata: $this->buildStripeMetadata(
                orderId: $orderId,
                paymentId: $paymentId,
                programId: $programData->program->programId,
                userId: $userId,
                firstName: $payload->firstName,
                lastName: $payload->lastName,
            ),
        );
    }

    private function linkStripeIds(int $paymentId, array $session, string $sessionId): void
    {
        $this->paymentRepository->updateStripeSessionId($paymentId, $sessionId);
        $this->paymentRepository->updateProviderRef($paymentId, $sessionId);

        $paymentIntentId = $session['payment_intent'] ?? null;
        if (is_string($paymentIntentId) && $paymentIntentId !== '') {
            $this->paymentRepository->updateStripePaymentIntentId($paymentId, $paymentIntentId);
        }
    }

    public function getRetryOrder(int $orderId, int $userId): \App\Models\Order
    {
        $order = $this->orderRepository->findByIdAndUserId($orderId, $userId);

        if ($order === null) {
            throw new NotFoundException('Order', $orderId);
        }

        return $order;
    }

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

    /** @return array<string, mixed> */
    private function buildRetryStripeParams(\App\Models\Order $order, int $paymentId, PaymentMethod $method): array
    {
        return $this->buildStripeCheckoutParams(
            orderId: $order->orderId,
            paymentId: $paymentId,
            method: $method,
            total: (float) $order->totalAmount,
            orderNumber: $order->orderNumber,
            customerEmail: $order->ticketRecipientEmail ?? '',
            metadata: $this->buildStripeMetadata(
                orderId: $order->orderId,
                paymentId: $paymentId,
                programId: $order->programId,
                userId: $order->userAccountId,
                firstName: (string) ($order->ticketRecipientFirstName ?? ''),
                lastName: (string) ($order->ticketRecipientLastName ?? ''),
            ),
        );
    }

    /** @param array<string, string> $metadata @return array<string, mixed> */
    private function buildStripeCheckoutParams(
        int $orderId,
        int $paymentId,
        PaymentMethod $method,
        float $total,
        string $orderNumber,
        string $customerEmail,
        array $metadata,
    ): array {
        return [
            'mode' => 'payment',
            ...$this->buildStripeUrls($orderId, $paymentId),
            'payment_method_types' => $this->mapStripePaymentMethodTypes($method),
            'line_items' => $this->buildStripeLineItems($total, $orderNumber),
            'customer_email' => $customerEmail,
            'client_reference_id' => $orderNumber,
            'metadata' => $metadata,
        ];
    }

    /** @return array{success_url: string, cancel_url: string} */
    private function buildStripeUrls(int $orderId, int $paymentId): array
    {
        $appUrl = $this->runtimeConfig->getAppUrl();

        return [
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
        ];
    }

    // All cast to strings because Stripe metadata is string-based.
    /** @return array{order_id: string, payment_id: string, program_id: string, user_id: string, first_name: string, last_name: string} */
    private function buildStripeMetadata(
        int $orderId,
        int $paymentId,
        int $programId,
        int $userId,
        string $firstName,
        string $lastName,
    ): array {
        return [
            'order_id' => (string) $orderId,
            'payment_id' => (string) $paymentId,
            'program_id' => (string) $programId,
            'user_id' => (string) $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }

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

    /** @throws \InvalidArgumentException */
    public function getSessionSummary(string $sessionId): CheckoutSessionSummary
    {
        if ($sessionId === '') {
            throw new \InvalidArgumentException('Missing Stripe session id.');
        }

        $session = $this->loadCheckoutSession($sessionId);
        $this->fulfillPaidOrderFromSession($session);

        return new CheckoutSessionSummary(
            orderReference: (string) ($session['client_reference_id'] ?? ''),
            amountTotal: isset($session['amount_total']) ? ((int) $session['amount_total'] / 100) : 0,
            currency: strtoupper((string) ($session['currency'] ?? 'eur')),
        );
    }

    /** @return array<string,mixed> */
    private function loadCheckoutSession(string $sessionId): array
    {
        try {
            return $this->stripeService->retrieveCheckoutSession($sessionId);
        } catch (\Throwable $error) {
            throw new CheckoutSessionException('Stripe checkout session could not be loaded.', 0, $error);
        }
    }

    // Fallback fulfillment when the webhook is unavailable in local/dev. Idempotent.
    /** @param array<string,mixed> $session */
    private function fulfillPaidOrderFromSession(array $session): void
    {
        if (!$this->isPaidSession($session)) {
            return;
        }

        $orderId = $this->extractOrderIdFromSession($session);
        if ($orderId === null) {
            return;
        }

        $this->markOrderPaidFromSession($session, $orderId);

        $this->ticketFulfillmentService->fulfillPaidOrder(
            $orderId,
            $this->extractCustomerEmailFromSession($session),
            $this->extractMetadataString($session, 'first_name'),
            $this->extractMetadataString($session, 'last_name'),
        );
    }

    // Idempotent — only transitions from Pending.
    private function markOrderPaidFromSession(array $session, int $orderId): void
    {
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Paid, [OrderStatus::Pending]);

        $paymentId = $this->extractPaymentIdFromSession($session);
        if ($paymentId !== null) {
            $this->paymentRepository->updateStatusIfCurrentIn(
                $paymentId,
                PaymentStatus::Paid,
                [PaymentStatus::Pending],
                new \DateTimeImmutable(),
            );
        }

        $programId = $this->extractProgramIdFromSession($session);
        if ($programId > 0) {
            $this->programRepository->markCheckedOut($programId);
        }
    }

    private function extractPaymentIdFromSession(array $session): ?int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['payment_id'])) {
            return null;
        }
        $id = (int) $metadata['payment_id'];
        return $id > 0 ? $id : null;
    }

    private function extractProgramIdFromSession(array $session): int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['program_id'])) {
            return 0;
        }
        return (int) $metadata['program_id'];
    }

    private function isPaidSession(array $session): bool
    {
        return ($session['payment_status'] ?? null) === 'paid';
    }

    private function extractOrderIdFromSession(array $session): ?int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['order_id'])) {
            return null;
        }

        $orderId = (int) $metadata['order_id'];
        return $orderId > 0 ? $orderId : null;
    }

    // customer_details checked first (most structured), customer_email as fallback.
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

    private function extractMetadataString(array $session, string $key): ?string
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata[$key]) || !is_string($metadata[$key])) {
            return null;
        }

        $value = trim($metadata[$key]);
        return $value !== '' ? $value : null;
    }

    // One line item is enough — Stripe only needs the final amount; our DB has the breakdown.
    /** @return array<int, array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}> */
    private function buildStripeLineItems(float $total, string $orderNumber): array
    {
        return [[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int) round($total * 100),
                'product_data' => ['name' => 'Haarlem Festival order ' . $orderNumber],
            ],
            'quantity' => 1,
        ]];
    }

    private function validatePayload(CheckoutPayloadData $payload): void
    {
        if (!filter_var($payload->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid email address.');
        }

        $this->mapPaymentMethod($payload->paymentMethod);
    }

    private function mapPaymentMethod(string $rawMethod): PaymentMethod
    {
        return match ($rawMethod) {
            'credit_card' => PaymentMethod::CreditCard,
            'ideal' => PaymentMethod::Ideal,
            default => throw new \InvalidArgumentException('Unsupported payment method.'),
        };
    }

    /** @return string[] */
    private function mapStripePaymentMethodTypes(PaymentMethod $method): array
    {
        return match ($method) {
            PaymentMethod::CreditCard => ['card'],
            PaymentMethod::Ideal => ['ideal'],
            default => ['card'],
        };
    }

    /** @param ProgramItemData[] $items @throws CheckoutException */
    private function validateItemAvailability(array $items): void
    {
        foreach ($items as $item) {
            if ($item->passTypeId !== null || $item->eventSessionId === null || $item->eventSessionId <= 0) {
                continue;
            }
            $this->validateSingleItemAvailability($item);
        }
    }

    private function validateSingleItemAvailability(ProgramItemData $item): void
    {
        $capacity = $this->eventSessionRepository->getCapacityInfo($item->eventSessionId);

        if ($capacity === null) {
            throw new CheckoutInputException("Session for '{$item->eventTitle}' no longer exists.");
        }

        $available = $capacity->availableSeats();
        $this->validateSeatAvailability($item, $available);
        $this->validateSingleTicketCap($item, $capacity);
    }

    private function validateSeatAvailability(ProgramItemData $item, int $available): void
    {
        if ($available <= 0) {
            throw new CheckoutInputException("'{$item->eventTitle}' is sold out.");
        }

        $seatCount = $this->calculateSeatCount($item->quantity, $item->priceTierId);

        if ($seatCount > $available) {
            throw new CheckoutInputException(
                "Only {$available} seat(s) remaining for '{$item->eventTitle}'. You requested {$seatCount}."
            );
        }
    }

    // Uses configured limit if set, otherwise falls back to 90% of total capacity.
    private function validateSingleTicketCap(ProgramItemData $item, SessionCapacityInfo $capacity): void
    {
        // Use configured limit if available, otherwise calculate from ratio
        $singleTicketCap = $capacity->capacitySingleTicketLimit > 0
            ? $capacity->capacitySingleTicketLimit
            : (int) floor($capacity->capacityTotal * CheckoutConstraints::SINGLE_TICKET_CAPACITY_RATIO);

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

    public function getCheckoutMainContent(): CheckoutMainContent
    {
        return $this->checkoutContentRepository->findCheckoutMainContent('checkout', 'main');
    }
}
