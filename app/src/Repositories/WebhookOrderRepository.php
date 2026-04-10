<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IWebhookOrderRepository;
use PDO;

/**
 * Transactional repository for Stripe webhook-driven order/payment state changes.
 *
 * Encapsulates the multi-table mutations that webhooks trigger (order + payment + program
 * + capacity) behind single methods so the handler service stays free of raw PDO calls.
 */
class WebhookOrderRepository implements IWebhookOrderRepository
{
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IPaymentRepository $paymentRepository,
        private readonly IProgramRepository $programRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly PDO $pdo,
    ) {}

    public function completePayment(
        int $orderId,
        int $paymentId,
        string $paymentIntentId,
        int $programId,
        \DateTimeImmutable $paidAtUtc,
        array $allowedOrderStatuses,
        array $allowedPaymentStatuses,
    ): void {
        $this->pdo->beginTransaction();

        try {
            $this->paymentRepository->updateStripePaymentIntentId($paymentId, $paymentIntentId);
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Paid, $allowedOrderStatuses);
            $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Paid, $allowedPaymentStatuses, $paidAtUtc);

            if ($programId > 0) {
                $this->programRepository->markCheckedOut($programId);
            }

            $this->pdo->commit();
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    public function failPayment(
        int $orderId,
        int $paymentId,
        array $allowedOrderStatuses,
        array $allowedPaymentStatuses,
    ): void {
        $this->pdo->beginTransaction();

        try {
            $this->restoreCapacity($orderId);
            $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Expired, $allowedOrderStatuses);
            $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Failed, $allowedPaymentStatuses);

            $this->pdo->commit();
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    private function restoreCapacity(int $orderId): void
    {
        foreach ($this->orderItemRepository->findByOrderId($orderId) as $item) {
            if ($item->eventSessionId === null || $item->eventSessionId <= 0 || $item->quantity <= 0) {
                continue;
            }

            $this->eventSessionRepository->restoreCapacity($item->eventSessionId, $item->quantity);
        }
    }
}
