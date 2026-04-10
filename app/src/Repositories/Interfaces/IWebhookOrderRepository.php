<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

/**
 * Transactional operations for Stripe webhook-driven order/payment state changes.
 *
 * Each method runs its mutations inside a single DB transaction so the handler
 * service stays free of raw PDO calls.
 */
interface IWebhookOrderRepository
{
    /**
     * Records a completed payment: transitions order to Paid, payment to Paid,
     * stores the payment-intent ID, and marks the program as checked out.
     *
     * @param OrderStatus[] $allowedOrderStatuses Statuses that qualify for the transition.
     * @param PaymentStatus[] $allowedPaymentStatuses Statuses that qualify for the transition.
     * @throws \RuntimeException If the transaction cannot be committed.
     */
    public function completePayment(
        int $orderId,
        int $paymentId,
        string $paymentIntentId,
        int $programId,
        \DateTimeImmutable $paidAtUtc,
        array $allowedOrderStatuses,
        array $allowedPaymentStatuses,
    ): void;

    /**
     * Records a failed/expired payment: restores seat capacity, transitions
     * the order to Expired, and marks the payment as Failed.
     *
     * @param OrderStatus[] $allowedOrderStatuses Statuses that qualify for the transition.
     * @param PaymentStatus[] $allowedPaymentStatuses Statuses that qualify for the transition.
     * @throws \RuntimeException If the transaction cannot be committed.
     */
    public function failPayment(
        int $orderId,
        int $paymentId,
        array $allowedOrderStatuses,
        array $allowedPaymentStatuses,
    ): void;
}
