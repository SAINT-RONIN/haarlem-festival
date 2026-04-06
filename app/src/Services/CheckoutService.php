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
use App\Repositories\Interfaces\IProgramRepository;
use App\DTOs\Checkout\CheckoutCancelResult;
use App\DTOs\Checkout\CheckoutSessionResult;
use App\DTOs\Checkout\CheckoutSessionSummary;
use App\Exceptions\CheckoutException;
use App\Exceptions\CheckoutInputException;
use App\Exceptions\CheckoutSessionException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RetryPaymentException;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\DTOs\Events\SessionCapacityInfo;
use PDO;

/**
 * Orchestrates the full checkout flow from program validation to Stripe redirection.
 *
 * This service exists because checkout is a multi-step business process, not a single query:
 * it has to validate the cart, create local order and payment rows, reserve capacity,
 * and then hand the customer over to Stripe with enough metadata for later callbacks.
 * Webhook handling is split into another service so this class stays focused on
 * the user-driven checkout path.
 */
class CheckoutService implements ICheckoutService
{
    /**
     * Stores the dependencies needed for checkout persistence, capacity reservation, and Stripe.
     *
     * The constructor returns nothing because its only responsibility is setup:
     * receiving collaborators once so the actual checkout methods can stay explicit and readable.
     */
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
    ) {
    }

    /**
     * Validates the payload, persists an order with line items, creates a Stripe
     * checkout session, and returns the redirect URL for the payment gateway.
     *
     * It returns CheckoutSessionResult because the caller needs more than the redirect URL:
     * it also needs the created order and payment ids for later cancel, retry, and success flows.
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

    /**
     * Rejects totals that cannot produce a valid payment.
     *
     * It returns nothing because this is a guard method: invalid totals should stop checkout
     * immediately instead of being passed through the rest of the flow.
     */
    private function validatePositiveTotal(float $total): void
    {
        if ($total <= 0) {
            throw new CheckoutInputException('Total amount must be greater than zero.');
        }
    }

    /**
     * Returns the finished checkout result after all database writes and Stripe setup succeed.
     *
     * The transaction matters because order creation, order items, seat reservations,
     * and payment creation belong to one unit of work. If one step fails, none of them
     * should remain half-finished in the database.
     */
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

    /**
     * Ensures checkout only starts when the visitor actually has items in the program.
     *
     * It returns nothing because an empty program is a hard stop, not a warning the caller
     * should try to work around inside the checkout flow.
     */
    private function validateProgramNotEmpty(ProgramData $programData): void
    {
        if ($programData->program === null || $programData->items === []) {
            throw new CheckoutInputException('Your program is empty.');
        }
    }

    /**
     * Creates the order header row and returns the new order id.
     *
     * Only the header is stored here because line items, seat reservations, and payments
     * all depend on the generated order id and happen in later steps.
     */
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

    /**
     * Handles the extra write needed for pass products before the order item is created.
     *
     * It returns nothing because the important result is the persisted relationship:
     * first a PassPurchase row, then an OrderItem row that points to it.
     */
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
     * The returned string is the exact URL the browser should redirect to because Stripe
     * owns the next stage of payment once local checkout setup is complete.
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
        return $this->buildStripeCheckoutParams(
            orderId: $orderId,
            paymentId: $paymentId,
            method: $method,
            total: $programData->total,
            orderNumber: $orderNumber,
            customerEmail: (string)$payload['email'],
            metadata: $this->buildStripeMetadata(
                orderId: $orderId,
                paymentId: $paymentId,
                programId: $programData->program->programId,
                userId: $userId,
                firstName: (string)$payload['firstName'],
                lastName: (string)$payload['lastName'],
            ),
        );
    }

    /**
     * Saves Stripe identifiers onto the local payment row after session creation.
     *
     * It returns nothing because the useful result is the stored linkage itself:
     * later webhook and cancel flows depend on these ids already being saved locally.
     */
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
     * Returns the order the current user is allowed to retry payment for.
     *
     * Throwing when the order is missing keeps the retry flow simple:
     * the rest of the code can safely assume a real, user-owned order exists.
     */
    public function getRetryOrder(int $orderId, int $userId): \App\Models\Order
    {
        $order = $this->orderRepository->findByIdAndUserId($orderId, $userId);

        if ($order === null) {
            throw new NotFoundException('Order', $orderId);
        }

        return $order;
    }

    /**
     * Creates a fresh payment attempt for an existing order and returns the new checkout result.
     *
     * The result includes a new payment id because each retry is tracked as its own payment record,
     * even though the order itself stays the same.
     */
    public function retryCheckoutSession(int $orderId, int $userId, array $payload): CheckoutSessionResult
    {
        $order = $this->getRetryOrder($orderId, $userId);
        $this->validateRetryEligibility($order);

        $method = $this->mapPaymentMethod((string) ($payload['paymentMethod'] ?? ''));

        return $this->executeRetryTransaction($order, $method);
    }

    /**
     * Confirms the order can still be retried.
     *
     * It returns nothing because retry eligibility is a rule check:
     * either the order is valid for retry or the method throws immediately.
     */
    private function validateRetryEligibility(\App\Models\Order $order): void
    {
        if ($order->status !== OrderStatus::Pending) {
            throw new RetryPaymentException('This order is no longer pending.');
        }

        if ($order->payBeforeUtc !== null && $order->payBeforeUtc < new \DateTimeImmutable('now')) {
            throw new RetryPaymentException('The payment deadline has passed.');
        }
    }

    /**
     * Returns the retry checkout result after creating a new payment row and Stripe session.
     *
     * The transaction exists because a retry payment should never be left behind without
     * a usable Stripe session linked to it.
     */
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

    /**
     * Returns the Stripe checkout URL for a retry payment.
     *
     * The response is validated here so later code can safely assume Stripe returned both
     * a session id and a redirect URL before the payment row is considered usable.
     */
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
     * Returns the Stripe payload for a retry payment attempt.
     *
     * It reuses the shared builder because a retry payment should behave like the original checkout,
     * just with an existing order instead of a freshly created one.
     *
     * @return array<string, mixed>
     */
    private function buildRetryStripeParams(\App\Models\Order $order, int $paymentId, PaymentMethod $method): array
    {
        return $this->buildStripeCheckoutParams(
            orderId: $order->orderId,
            paymentId: $paymentId,
            method: $method,
            total: (float)$order->totalAmount,
            orderNumber: $order->orderNumber,
            customerEmail: $order->ticketRecipientEmail ?? '',
            metadata: $this->buildStripeMetadata(
                orderId: $order->orderId,
                paymentId: $paymentId,
                programId: $order->programId,
                userId: $order->userAccountId,
                firstName: (string)($order->ticketRecipientFirstName ?? ''),
                lastName: (string)($order->ticketRecipientLastName ?? ''),
            ),
        );
    }

    /**
     * Returns the final Stripe checkout payload shared by new and retry payments.
     *
     * Centralizing this array matters because new and retry checkouts should only differ
     * in their source data, not in the Stripe contract they send.
     *
     * @param array<string, string> $metadata
     * @return array<string, mixed>
     */
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

    /**
     * Returns the Stripe success and cancel URLs for one checkout attempt.
     *
     * The urls include our order and payment ids because the app needs those values later
     * to cancel pending rows or reconnect the returning customer to the right payment.
     *
     * @return array{success_url: string, cancel_url: string}
     */
    private function buildStripeUrls(int $orderId, int $paymentId): array
    {
        $appUrl = $this->runtimeConfig->getAppUrl();

        return [
            'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
        ];
    }

    /**
     * Returns the metadata stored on the Stripe session so later callbacks can find our records.
     *
     * Everything is cast to strings because Stripe metadata is string-based, and webhook code
     * later reads those same values back out of the Stripe payload.
     *
     * @return array{
     *     order_id: string,
     *     payment_id: string,
     *     program_id: string,
     *     user_id: string,
     *     first_name: string,
     *     last_name: string
     * }
     */
    private function buildStripeMetadata(
        int $orderId,
        int $paymentId,
        int $programId,
        int $userId,
        string $firstName,
        string $lastName,
    ): array {
        return [
            'order_id' => (string)$orderId,
            'payment_id' => (string)$paymentId,
            'program_id' => (string)$programId,
            'user_id' => (string)$userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }

    /**
     * Cancels the pending order and payment when the customer abandons checkout.
     *
     * It returns CheckoutCancelResult because the controller still needs a small, display-ready
     * summary of what was cancelled. Status guards prevent this method from overwriting rows
     * already changed by a webhook or another process.
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
     * The returned summary is intentionally smaller than the raw Stripe response
     * because the view only needs a few safe, display-ready values.
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
     * Returns the raw Stripe checkout session for the given session id.
     *
     * Wrapping the Stripe call here means the rest of the service only has to deal
     * with one project-specific exception type when session retrieval fails.
     *
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
     * It returns nothing because the useful outcome is the side effect:
     * local order state is corrected and ticket fulfillment can proceed.
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

        $this->markOrderPaidFromSession($session, $orderId);

        $this->ticketFulfillmentService->fulfillPaidOrder(
            $orderId,
            $this->extractCustomerEmailFromSession($session),
            $this->extractMetadataString($session, 'first_name'),
            $this->extractMetadataString($session, 'last_name'),
        );
    }

    /**
     * Marks the order and payment as Paid when the success page confirms payment.
     * Idempotent — only transitions from Pending, so webhook + success-page can both fire safely.
     */
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

    /**
     * Returns the internal payment id stored in Stripe metadata, or null when it is missing.
     *
     * Null is acceptable here because success-page handling can still continue partially
     * even if the payment id was not present in the returned metadata.
     */
    private function extractPaymentIdFromSession(array $session): ?int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['payment_id'])) {
            return null;
        }
        $id = (int) $metadata['payment_id'];
        return $id > 0 ? $id : null;
    }

    /**
     * Returns the internal program id stored in Stripe metadata, or zero when none exists.
     *
     * Zero is used as the "no valid program id" marker because later code only needs to know
     * whether there is a positive id worth marking as checked out.
     */
    private function extractProgramIdFromSession(array $session): int
    {
        $metadata = $session['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['program_id'])) {
            return 0;
        }
        return (int) $metadata['program_id'];
    }

    /**
     * Returns true only when Stripe reported this session as paid.
     *
     * This helper exists so later fulfillment code reads like a business rule
     * instead of a raw array comparison.
     *
     * @param array<string,mixed> $session
     */
    private function isPaidSession(array $session): bool
    {
        return ($session['payment_status'] ?? null) === 'paid';
    }

    /**
     * Returns the internal order id stored in Stripe metadata, or null when it is missing.
     *
     * Null is returned on purpose because callers use it as a signal to stop gracefully
     * when the Stripe session does not contain enough metadata to continue fulfillment.
     *
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
     * Returns the best available customer email from the Stripe session payload.
     *
     * customer_details is checked first because it is the most structured source;
     * customer_email is kept as a fallback when that block is absent.
     *
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
     * Returns one trimmed metadata value from the Stripe session, or null when it is missing.
     *
     * Returning null keeps callers simple because optional metadata such as first and last name
     * should not break fulfillment when Stripe sends them back empty.
     *
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
     * Returns the single line-item array sent to Stripe for this order total.
     *
     * One line item is enough because Stripe only needs the final amount for payment,
     * while the detailed order breakdown already lives in our own database.
     *
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
     * Validates the posted checkout payload before any database work starts.
     *
     * It returns nothing because invalid form data should stop checkout immediately
     * instead of being turned into a partial result object.
     *
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

    /**
     * Returns the internal PaymentMethod enum for the posted payment string.
     *
     * Converting to an enum early makes later code safer because the rest of checkout
     * no longer has to work with loose string values.
     */
    private function mapPaymentMethod(string $rawMethod): PaymentMethod
    {
        return match ($rawMethod) {
            'credit_card' => PaymentMethod::CreditCard,
            'ideal' => PaymentMethod::Ideal,
            default => throw new \InvalidArgumentException('Unsupported payment method.'),
        };
    }

    /**
     * Returns the Stripe payment-method type list for the chosen internal payment method.
     *
     * The result is an array because Stripe expects its method types in that format,
     * even when only one option is allowed.
     *
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
     * It returns nothing because the useful outcome is permission to continue checkout:
     * when any item fails validation, the method throws and the flow stops.
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

    /**
     * Validates one session-based program item against all capacity rules.
     *
     * Keeping this logic in a separate method makes the rule set easier to explain
     * than embedding everything inside the cart loop.
     */
    private function validateSingleItemAvailability(ProgramItemData $item): void
    {
        $capacity = $this->eventSessionRepository->getCapacityInfo($item->eventSessionId);

        if ($capacity === null) {
            throw new CheckoutInputException("Session for '{$item->eventTitle}' no longer exists.");
        }

        $available = $this->calculateAvailableSeats($capacity);
        $this->validateSeatAvailability($item, $available);
        $this->validateSingleTicketCap($item, $capacity);
    }

    /** Returns remaining seats for one session from the repository capacity snapshot. */
    private function calculateAvailableSeats(SessionCapacityInfo $capacity): int
    {
        return max(0, $capacity->capacityTotal - $capacity->soldSingleTickets - $capacity->soldReservedSeats);
    }

    /**
     * Checks whether the requested quantity still fits within remaining seats.
     *
     * It throws user-facing input errors because this is a recoverable checkout problem:
     * the visitor can usually fix it by reducing quantity or removing the item.
     */
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

    /**
     * Enforces the single-ticket cap that keeps some capacity available for pass holders.
     *
     * It returns nothing because this is another guard rule:
     * either the item respects the cap or checkout stops with a clear explanation.
     */
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

    /**
     * Cancels the order when it exists and restores any seats that were reserved for it.
     *
     * It returns nothing because the important result is the cleaned-up database state,
     * not a value handed back to the caller.
     */
    private function cancelOrderIfPresent(?int $orderId): void
    {
        if ($orderId === null) {
            return;
        }

        $this->orderCapacityRestorer->restore($orderId);
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
    }

    /**
     * Cancels the payment row when a matching pending payment exists.
     *
     * This is separate from order cancellation because one identifier may be present
     * while the other is missing.
     */
    private function cancelPaymentIfPresent(?int $paymentId): void
    {
        if ($paymentId === null) {
            return;
        }

        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
    }

    /**
     * Returns a money string in the decimal format expected by the repository layer.
     *
     * The repositories accept money as strings so decimal precision is preserved
     * instead of relying on raw floating-point values.
     */
    private function toMoneyString(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Returns a unique order reference for customers, support staff, and Stripe metadata.
     *
     * The format combines a timestamp and random bytes so the number stays readable
     * while still being very unlikely to collide.
     */
    private function generateOrderNumber(): string
    {
        return 'HF-' . gmdate('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /**
     * Returns the CMS-managed text and labels for the checkout page.
     *
     * This method exists so controllers do not need to know where checkout copy lives;
     * they just request one typed content object from the service layer.
     */
    public function getCheckoutMainContent(): CheckoutMainContent
    {
        return $this->checkoutContentRepository->findCheckoutMainContent('checkout', 'main');
    }
}
