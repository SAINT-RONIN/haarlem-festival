<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CheckoutConstraints;
use App\Enums\PriceTierId;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Infrastructure\Interfaces\IStripeService;
use App\DTOs\Domain\Program\ProgramData;
use App\DTOs\Domain\Program\ProgramItemData;
use App\DTOs\Domain\Events\SessionCapacityInfo;
use App\DTOs\Cms\CheckoutMainContent;
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\ICheckoutOrderRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\DTOs\Domain\Checkout\CheckoutCancelResult;
use App\DTOs\Domain\Checkout\CheckoutPayloadData;
use App\DTOs\Domain\Checkout\CheckoutSessionResult;
use App\DTOs\Domain\Checkout\CheckoutSessionSummary;
use App\Exceptions\CheckoutInputException;
use App\Exceptions\CheckoutSessionException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RetryPaymentException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use App\Services\Interfaces\ITicketFulfillmentService;

class CheckoutService implements ICheckoutService
{
    public function __construct(
        private readonly ICheckoutOrderRepository $checkoutOrderRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly IStripeService $stripeService,
        private readonly ICheckoutRuntimeConfig $runtimeConfig,
        private readonly ICheckoutContentRepository $checkoutContentRepository,
        private readonly ITicketFulfillmentService $ticketFulfillmentService,
    ) {}

    /** @throws CheckoutInputException|\InvalidArgumentException */
    public function createCheckoutSession(ProgramData $programData, int $userId, CheckoutPayloadData $payload): CheckoutSessionResult
    {
        $this->validatePayload($payload);
        $this->validateProgramNotEmpty($programData);
        $this->validatePositiveTotal($programData->total);
        $this->validateItemAvailability($programData->items);

        $method = $this->mapPaymentMethod($payload->paymentMethod);
        $orderNumber = $this->generateOrderNumber();
        $vatRate = $this->toMoneyString($this->runtimeConfig->getVatRate() * 100);

        $items = $this->buildOrderItems($programData->items);

        $result = $this->checkoutOrderRepository->createOrder(
            userId: $userId,
            programId: $programData->program->programId,
            orderNumber: $orderNumber,
            subtotal: $this->toMoneyString($programData->subtotal),
            vatTotal: $this->toMoneyString($programData->taxAmount),
            totalAmount: $this->toMoneyString($programData->total),
            firstName: $payload->firstName,
            lastName: $payload->lastName,
            email: $payload->email,
            payBeforeUtc: new \DateTimeImmutable('+24 hours'),
            paymentMethod: $method,
            vatRate: $vatRate,
            items: $items,
        );

        $checkoutUrl = $this->createAndLinkStripeSession(
            $programData->total,
            $userId,
            $programData->program->programId,
            $payload,
            $result['orderId'],
            $result['paymentId'],
            $orderNumber,
            $method,
        );

        return new CheckoutSessionResult(redirectUrl: $checkoutUrl, orderId: $result['orderId'], paymentId: $result['paymentId']);
    }

    public function handleCancel(?int $orderId, ?int $paymentId): CheckoutCancelResult
    {
        if ($orderId !== null && $paymentId !== null) {
            $this->checkoutOrderRepository->cancelOrder($orderId, $paymentId);
        }

        return new CheckoutCancelResult(
            status: 'cancelled',
            orderId: $orderId,
            paymentId: $paymentId,
        );
    }

    public function getRetryOrder(int $orderId, int $userId): \App\Models\Order
    {
        $order = $this->checkoutOrderRepository->findOrderByIdAndUserId($orderId, $userId);

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
        $paymentId = $this->checkoutOrderRepository->createRetryPayment($order->orderId, $method);

        $checkoutUrl = $this->createAndLinkRetryStripeSession($order, $paymentId, $method);

        return new CheckoutSessionResult(
            redirectUrl: $checkoutUrl,
            orderId: $order->orderId,
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

    public function getCheckoutMainContent(): CheckoutMainContent
    {
        return $this->checkoutContentRepository->findCheckoutMainContent('checkout', 'main');
    }

    // --- Validation ---

    private function validatePayload(CheckoutPayloadData $payload): void
    {
        if (!filter_var($payload->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid email address.');
        }

        $this->mapPaymentMethod($payload->paymentMethod);
    }

    private function validateProgramNotEmpty(ProgramData $programData): void
    {
        if ($programData->program === null || $programData->items === []) {
            throw new CheckoutInputException('Your program is empty.');
        }
    }

    // Guards against zero, negative, NaN, and Infinity.
    private function validatePositiveTotal(float $total): void
    {
        if (is_nan($total) || is_infinite($total)) {
            throw new CheckoutInputException('Total amount is invalid.');
        }

        if ($total <= 0) {
            throw new CheckoutInputException('Total amount must be greater than zero.');
        }
    }

    /** @param ProgramItemData[] $items @throws CheckoutInputException */
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

    private function validateRetryEligibility(\App\Models\Order $order): void
    {
        if ($order->status !== OrderStatus::Pending) {
            throw new RetryPaymentException('This order is no longer pending.');
        }

        if ($order->payBeforeUtc !== null && $order->payBeforeUtc < new \DateTimeImmutable('now')) {
            throw new RetryPaymentException('The payment deadline has passed.');
        }
    }

    // --- Stripe session creation ---

    private function createAndLinkStripeSession(
        float $total,
        int $userId,
        int $programId,
        CheckoutPayloadData $payload,
        int $orderId,
        int $paymentId,
        string $orderNumber,
        PaymentMethod $method,
    ): string {
        $params = $this->buildStripeCheckoutParams(
            orderId: $orderId,
            paymentId: $paymentId,
            method: $method,
            total: $total,
            orderNumber: $orderNumber,
            customerEmail: $payload->email,
            metadata: $this->buildStripeMetadata($orderId, $paymentId, $programId, $userId, $payload->firstName, $payload->lastName),
        );

        $session = $this->callStripeCreateCheckout($params);

        $sessionId = (string) ($session['id'] ?? '');
        $checkoutUrl = (string) ($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutSessionException('Stripe checkout session could not be created.');
        }

        $paymentIntentId = is_string($session['payment_intent'] ?? null) ? $session['payment_intent'] : '';
        $this->checkoutOrderRepository->linkStripeIds($paymentId, $sessionId, $paymentIntentId);

        return $checkoutUrl;
    }

    private function createAndLinkRetryStripeSession(\App\Models\Order $order, int $paymentId, PaymentMethod $method): string
    {
        $params = $this->buildStripeCheckoutParams(
            orderId: $order->orderId,
            paymentId: $paymentId,
            method: $method,
            total: (float) $order->totalAmount,
            orderNumber: $order->orderNumber,
            customerEmail: $order->ticketRecipientEmail ?? '',
            metadata: $this->buildStripeMetadata(
                $order->orderId,
                $paymentId,
                $order->programId,
                $order->userAccountId,
                (string) ($order->ticketRecipientFirstName ?? ''),
                (string) ($order->ticketRecipientLastName ?? ''),
            ),
        );

        $session = $this->callStripeCreateCheckout($params);

        $sessionId = (string) ($session['id'] ?? '');
        $checkoutUrl = (string) ($session['url'] ?? '');

        if ($sessionId === '' || $checkoutUrl === '') {
            throw new CheckoutSessionException('Stripe checkout session could not be created.');
        }

        $paymentIntentId = is_string($session['payment_intent'] ?? null) ? $session['payment_intent'] : '';
        $this->checkoutOrderRepository->linkStripeIds($paymentId, $sessionId, $paymentIntentId);

        return $checkoutUrl;
    }

    /** @return array<string, mixed> */
    private function callStripeCreateCheckout(array $params): array
    {
        try {
            return $this->stripeService->createCheckoutSession($params);
        } catch (\Throwable $error) {
            throw new CheckoutSessionException('Stripe checkout session could not be created.', 0, $error);
        }
    }

    // --- Fallback fulfillment (local/dev when webhooks aren't available) ---

    /** @param array<string,mixed> $session */
    private function fulfillPaidOrderFromSession(array $session): void
    {
        if (($session['payment_status'] ?? null) !== 'paid') {
            return;
        }

        $orderId = $this->extractIntMetadata($session, 'order_id');
        $paymentId = $this->extractIntMetadata($session, 'payment_id');
        $programId = $this->extractIntMetadata($session, 'program_id');

        if ($orderId === null) {
            return;
        }

        $this->checkoutOrderRepository->markOrderPaid(
            $orderId,
            $paymentId ?? 0,
            $programId ?? 0,
            new \DateTimeImmutable(),
        );

        $this->ticketFulfillmentService->fulfillPaidOrder(
            $orderId,
            $this->extractCustomerEmail($session),
            $this->extractMetadataString($session, 'first_name'),
            $this->extractMetadataString($session, 'last_name'),
        );
    }

    // --- Helpers ---

    /** @param ProgramItemData[] $items @return array{eventSessionId: ?int, passTypeId: ?int, quantity: int, seatCount: int, basePrice: float, donationAmount: float, passValidDate: ?string}[] */
    private function buildOrderItems(array $items): array
    {
        return array_map(fn(ProgramItemData $item) => [
            'eventSessionId' => $item->eventSessionId,
            'passTypeId' => $item->passTypeId,
            'quantity' => $item->quantity,
            'seatCount' => $this->calculateSeatCount($item->quantity, $item->priceTierId),
            'basePrice' => $item->basePrice,
            'donationAmount' => $item->donationAmount,
            'passValidDate' => $item->passValidDate,
        ], $items);
    }

    private function calculateSeatCount(int $quantity, ?int $priceTierId): int
    {
        if ($priceTierId !== null && in_array($priceTierId, [PriceTierId::Family->value, PriceTierId::Group->value], true)) {
            return $quantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT;
        }

        return $quantity;
    }

    private function mapPaymentMethod(string $rawMethod): PaymentMethod
    {
        return match ($rawMethod) {
            'credit_card' => PaymentMethod::CreditCard,
            'ideal' => PaymentMethod::Ideal,
            default => throw new \InvalidArgumentException('Unsupported payment method.'),
        };
    }

    /** @return array<string, mixed> */
    private function buildStripeCheckoutParams(
        int $orderId,
        int $paymentId,
        PaymentMethod $method,
        float $total,
        string $orderNumber,
        string $customerEmail,
        array $metadata,
    ): array {
        $appUrl = $this->runtimeConfig->getAppUrl();

        return [
            'mode' => 'payment',
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
            'payment_method_types' => match ($method) {
                PaymentMethod::CreditCard => ['card'],
                PaymentMethod::Ideal => ['ideal'],
                default => ['card'],
            },
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($total * 100),
                    'product_data' => ['name' => 'Haarlem Festival order ' . $orderNumber],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $customerEmail,
            'client_reference_id' => $orderNumber,
            'metadata' => $metadata,
        ];
    }

    /** @return array<string, string> */
    private function buildStripeMetadata(int $orderId, int $paymentId, int $programId, int $userId, string $firstName, string $lastName): array
    {
        return [
            'order_id' => (string) $orderId,
            'payment_id' => (string) $paymentId,
            'program_id' => (string) $programId,
            'user_id' => (string) $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
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

    // Prefers structured customer_details, falls back to customer_email.
    private function extractCustomerEmail(array $session): ?string
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

    private function extractIntMetadata(array $session, string $key): ?int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata[$key])) {
            return null;
        }

        $id = (int) $metadata[$key];
        return $id > 0 ? $id : null;
    }

    private function toMoneyString(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    private function generateOrderNumber(): string
    {
        return 'HF-' . gmdate('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
