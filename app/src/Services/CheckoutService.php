<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Interfaces\IStripeService;
use App\Models\ProgramData;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
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
        private readonly IStripeWebhookEventRepository $webhookEventRepository,
        private readonly IStripeService $stripeService,
        private readonly ICheckoutRuntimeConfig $runtimeConfig,
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Validates the payload, persists an order with line items, creates a Stripe
     * checkout session, and returns the redirect URL for the payment gateway.
     *
     * @param array{firstName:string,lastName:string,email:string,paymentMethod:string,saveDetails?:bool} $payload
     * @return array{redirectUrl:string,orderId:int,paymentId:int}
     * @throws CheckoutException When the program is empty or Stripe session creation fails
     * @throws \InvalidArgumentException When required payload fields are missing or invalid
     */
    public function createCheckoutSession(ProgramData $programData, int $userId, array $payload): array
    {
        // Validate required fields and payment method before starting any DB work
        $this->validatePayload($payload);

        $program = $programData->program;
        $items = $programData->items;

        if ($program === null || $items === []) {
            throw new CheckoutException('Your program is empty.');
        }

        // Resolve pricing and payment method from the enriched program data
        $method = $this->mapPaymentMethod((string)$payload['paymentMethod']);
        $subtotal = $programData->subtotal;
        $vatTotal = $programData->taxAmount;
        $total = $programData->total;

        if ($total <= 0) {
            throw new CheckoutException('Total amount must be greater than zero.');
        }

        $appUrl = $this->runtimeConfig->getAppUrl();
        $orderId = 0;
        $paymentId = 0;

        $this->pdo->beginTransaction();

        try {
            // Persist the order header with a unique order number
            $orderNumber = $this->generateOrderNumber();
            $payBeforeUtc = new \DateTimeImmutable('+24 hours');

            $orderId = $this->orderRepository->create(
                userAccountId: $userId,
                programId: $program->programId,
                orderNumber: $orderNumber,
                subtotal: $this->toMoneyString($subtotal),
                vatTotal: $this->toMoneyString($vatTotal),
                totalAmount: $this->toMoneyString($total),
                payBeforeUtc: $payBeforeUtc,
            );

            // Persist each program item as an order line item
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

            // Create a pending payment record before calling Stripe
            $paymentId = $this->paymentRepository->create($orderId, $method, PaymentStatus::Pending);

            // Build the Stripe line items (single consolidated line for the full total)
            $lineItems = [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int)round($total * 100),
                    'product_data' => [
                        'name' => 'Haarlem Festival order ' . $orderNumber,
                    ],
                ],
                'quantity' => 1,
            ]];

            // Create the Stripe checkout session with order metadata for webhook reconciliation
            $session = $this->stripeService->createCheckoutSession([
                'mode' => 'payment',
                'success_url' => $appUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $appUrl . '/checkout/cancel?order_id=' . $orderId . '&payment_id=' . $paymentId,
                'payment_method_types' => $this->mapStripePaymentMethodTypes($method),
                'line_items' => $lineItems,
                'customer_email' => (string)$payload['email'],
                'client_reference_id' => $orderNumber,
                'metadata' => [
                    'order_id' => (string)$orderId,
                    'payment_id' => (string)$paymentId,
                    'program_id' => (string)$program->programId,
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

            // Link Stripe identifiers back to our payment record for later webhook matching
            $this->paymentRepository->updateStripeSessionId($paymentId, $sessionId);
            $this->paymentRepository->updateProviderRef($paymentId, $sessionId);

            $paymentIntentId = $session['payment_intent'] ?? null;
            if (is_string($paymentIntentId) && $paymentIntentId !== '') {
                $this->paymentRepository->updateStripePaymentIntentId($paymentId, $paymentIntentId);
            }

            $this->pdo->commit();

            return [
                'redirectUrl' => $checkoutUrl,
                'orderId' => $orderId,
                'paymentId' => $paymentId,
            ];
        } catch (CheckoutException|\InvalidArgumentException|\RuntimeException $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Marks a pending order and its payment as cancelled when the user abandons checkout.
     * Only transitions records that are still in Pending status to avoid overwriting webhook updates.
     *
     * @return array{status:string,orderId:?int,paymentId:?int}
     */
    public function handleCancel(?int $orderId, ?int $paymentId): array
    {
        if ($orderId !== null) {
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
        }

        if ($paymentId !== null) {
            $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
        }

        return [
            'status' => 'cancelled',
            'orderId' => $orderId,
            'paymentId' => $paymentId,
        ];
    }

    /**
     * Processes an incoming Stripe webhook event. Verifies the signature, ensures
     * idempotency via the webhook event repository, and transitions order/payment
     * statuses based on the event type (completed, expired, or failed).
     *
     * @return array{processed:bool,eventId:string,eventType:string}
     * @throws CheckoutException When the event payload is invalid or malformed
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): array
    {
        // Verify signature and decode the Stripe event
        $event = $this->stripeService->constructWebhookEvent($payload, $signatureHeader);

        $eventId = (string)($event['id'] ?? '');
        $eventType = (string)($event['type'] ?? '');

        if ($eventId === '' || $eventType === '') {
            throw new CheckoutException('Invalid Stripe event payload.');
        }

        // Idempotency guard: skip already-processed events
        if ($this->webhookEventRepository->hasProcessed($eventId)) {
            return [
                'processed' => false,
                'eventId' => $eventId,
                'eventType' => $eventType,
            ];
        }

        // Extract order/payment IDs from the event metadata for status transitions
        $object = $event['data']['object'] ?? null;
        if (!is_array($object)) {
            throw new CheckoutException('Stripe event object is missing.');
        }

        $metadata = isset($object['metadata']) && is_array($object['metadata']) ? $object['metadata'] : [];
        $orderId = isset($metadata['order_id']) ? (int)$metadata['order_id'] : null;
        $paymentId = isset($metadata['payment_id']) ? (int)$metadata['payment_id'] : null;

        $this->pdo->beginTransaction();

        try {
            if ($paymentId !== null && isset($object['payment_intent']) && is_string($object['payment_intent']) && $object['payment_intent'] !== '') {
                $this->paymentRepository->updateStripePaymentIntentId($paymentId, $object['payment_intent']);
            }

            switch ($eventType) {
                case 'checkout.session.completed':
                case 'checkout.session.async_payment_succeeded':
                    if ($orderId !== null) {
                        $this->orderRepository->updateStatus($orderId, OrderStatus::Paid);
                    }
                    if ($paymentId !== null) {
                        $this->paymentRepository->updateStatus($paymentId, PaymentStatus::Paid, new \DateTimeImmutable());
                    }
                    if (isset($metadata['program_id'])) {
                        $programId = (int)$metadata['program_id'];
                        if ($programId > 0) {
                            $this->programRepository->markCheckedOut($programId);
                        }
                    }
                    break;

                case 'checkout.session.expired':
                case 'checkout.session.async_payment_failed':
                    if ($orderId !== null) {
                        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Expired, [OrderStatus::Pending]);
                    }
                    if ($paymentId !== null) {
                        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Failed, [PaymentStatus::Pending]);
                    }
                    break;

                default:
                    break;
            }

            $this->webhookEventRepository->markProcessed($eventId, $eventType);
            $this->pdo->commit();
        } catch (CheckoutException|\InvalidArgumentException|\RuntimeException $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }

        return [
            'processed' => true,
            'eventId' => $eventId,
            'eventType' => $eventType,
        ];
    }

    /**
     * Retrieves a Stripe checkout session and returns a normalized summary
     * used to display the order confirmation or failure page.
     *
     * @return array{sessionId:string,paymentStatus:string,status:string,amountTotal:float,currency:string}
     * @throws \InvalidArgumentException When sessionId is empty
     */
    public function getSessionSummary(string $sessionId): array
    {
        if ($sessionId === '') {
            throw new \InvalidArgumentException('Missing Stripe session id.');
        }

        $session = $this->stripeService->retrieveCheckoutSession($sessionId);

        return [
            'sessionId' => $session['id'] ?? '',
            'paymentStatus' => $session['payment_status'] ?? 'unpaid',
            'status' => $session['status'] ?? 'open',
            'amountTotal' => isset($session['amount_total']) ? ((int)$session['amount_total'] / 100) : 0,
            'currency' => strtoupper((string)($session['currency'] ?? 'eur')),
        ];
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

    private function toMoneyString(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    private function generateOrderNumber(): string
    {
        return 'HF-' . gmdate('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

}

