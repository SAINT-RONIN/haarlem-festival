<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Interfaces\IStripeService;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use App\Services\Interfaces\ICheckoutService;
use App\Services\Interfaces\ICheckoutRuntimeConfig;
use PDO;

class CheckoutService implements ICheckoutService
{
    private IProgramRepository $programRepository;
    private IOrderRepository $orderRepository;
    private IOrderItemRepository $orderItemRepository;
    private IPaymentRepository $paymentRepository;
    private IStripeWebhookEventRepository $webhookEventRepository;
    private IStripeService $stripeService;
    private ICheckoutRuntimeConfig $runtimeConfig;
    private PDO $pdo;

    public function __construct(
        IProgramRepository $programRepository,
        IOrderRepository $orderRepository,
        IOrderItemRepository $orderItemRepository,
        IPaymentRepository $paymentRepository,
        IStripeWebhookEventRepository $webhookEventRepository,
        IStripeService $stripeService,
        ICheckoutRuntimeConfig $runtimeConfig,
        PDO $pdo,
    ) {
        $this->programRepository = $programRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->paymentRepository = $paymentRepository;
        $this->webhookEventRepository = $webhookEventRepository;
        $this->stripeService = $stripeService;
        $this->runtimeConfig = $runtimeConfig;
        $this->pdo = $pdo;
    }

    public function createCheckoutSession(array $programData, int $userId, array $payload): array
    {
        $this->validatePayload($payload);

        $program = $programData['program'] ?? null;
        $items = $programData['items'] ?? [];

        if ($program === null || $items === []) {
            throw new \RuntimeException('Your program is empty.');
        }

        $method = $this->mapPaymentMethod((string)$payload['paymentMethod']);
        $subtotal = (float)$programData['subtotal'];
        $vatTotal = (float)$programData['taxAmount'];
        $total = (float)$programData['total'];

        if ($total <= 0) {
            throw new \RuntimeException('Total amount must be greater than zero.');
        }

        $appUrl = $this->runtimeConfig->getAppUrl();
        $orderId = 0;
        $paymentId = 0;

        $this->pdo->beginTransaction();

        try {
            $orderNumber = $this->generateOrderNumber();
            $payBeforeUtc = new \DateTimeImmutable('+30 minutes');

            $orderId = $this->orderRepository->create(
                userAccountId: $userId,
                programId: $program->programId,
                orderNumber: $orderNumber,
                subtotal: $this->toMoneyString($subtotal),
                vatTotal: $this->toMoneyString($vatTotal),
                totalAmount: $this->toMoneyString($total),
                payBeforeUtc: $payBeforeUtc,
            );

            foreach ($items as $item) {
                $this->orderItemRepository->create(
                    orderId: $orderId,
                    eventSessionId: isset($item['eventSessionId']) ? (int)$item['eventSessionId'] : null,
                    historyTourId: null,
                    passPurchaseId: null,
                    quantity: (int)$item['quantity'],
                    unitPrice: $this->toMoneyString((float)$item['basePrice']),
                    vatRate: $this->toMoneyString($this->runtimeConfig->getVatRate() * 100),
                    donationAmount: $this->toMoneyString((float)($item['donationAmount'] ?? 0.0)),
                );
            }

            $paymentId = $this->paymentRepository->create($orderId, $method, PaymentStatus::Pending);

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
                throw new \RuntimeException('Stripe checkout session could not be created.');
            }

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
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $error;
        }
    }

    public function handleCancel(?int $orderId, ?int $paymentId): array
    {
        if ($orderId !== null) {
            $this->updateOrderStatusIfCurrent($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
        }

        if ($paymentId !== null) {
            $this->updatePaymentStatusIfCurrent($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
        }

        return [
            'status' => 'cancelled',
            'orderId' => $orderId,
            'paymentId' => $paymentId,
        ];
    }

    public function handleWebhook(string $payload, ?string $signatureHeader): array
    {
        $event = $this->stripeService->constructWebhookEvent($payload, $signatureHeader);

        $eventId = (string)($event['id'] ?? '');
        $eventType = (string)($event['type'] ?? '');

        if ($eventId === '' || $eventType === '') {
            throw new \RuntimeException('Invalid Stripe event payload.');
        }

        if ($this->webhookEventRepository->hasProcessed($eventId)) {
            return [
                'processed' => false,
                'eventId' => $eventId,
                'eventType' => $eventType,
            ];
        }

        $object = $event['data']['object'] ?? null;
        if (!is_array($object)) {
            throw new \RuntimeException('Stripe event object is missing.');
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
                        $this->updateOrderStatusIfCurrent($orderId, OrderStatus::Expired, [OrderStatus::Pending]);
                    }
                    if ($paymentId !== null) {
                        $this->updatePaymentStatusIfCurrent($paymentId, PaymentStatus::Failed, [PaymentStatus::Pending]);
                    }
                    break;

                default:
                    break;
            }

            $this->webhookEventRepository->markProcessed($eventId, $eventType);
            $this->pdo->commit();
        } catch (\Throwable $error) {
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


    /**
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    private function updateOrderStatusIfCurrent(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void
    {
        if ($allowedCurrentStatuses === []) {
            return;
        }

        $inClause = implode(', ', array_fill(0, count($allowedCurrentStatuses), '?'));
        $sql = "UPDATE `Order` SET Status = ? WHERE OrderId = ? AND Status IN ({$inClause})";
        $stmt = $this->pdo->prepare($sql);

        $params = [$newStatus->value, $orderId];
        foreach ($allowedCurrentStatuses as $status) {
            $params[] = $status->value;
        }

        $stmt->execute($params);
    }

    /**
     * @param PaymentStatus[] $allowedCurrentStatuses
     */
    private function updatePaymentStatusIfCurrent(int $paymentId, PaymentStatus $newStatus, array $allowedCurrentStatuses): void
    {
        if ($allowedCurrentStatuses === []) {
            return;
        }

        $inClause = implode(', ', array_fill(0, count($allowedCurrentStatuses), '?'));
        $sql = "UPDATE Payment SET Status = ?, PaidAtUtc = ? WHERE PaymentId = ? AND Status IN ({$inClause})";
        $stmt = $this->pdo->prepare($sql);

        $paidAtUtc = $newStatus === PaymentStatus::Paid ? (new \DateTimeImmutable())->format('Y-m-d H:i:s') : null;

        $params = [$newStatus->value, $paidAtUtc, $paymentId];
        foreach ($allowedCurrentStatuses as $status) {
            $params[] = $status->value;
        }

        $stmt->execute($params);
    }
}

