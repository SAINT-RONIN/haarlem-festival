<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

/**
 * Defines persistence operations for payment records linked to orders.
 */
interface IPaymentRepository
{
    /**
     * Inserts a new payment record for an order and returns the generated payment ID.
     */
    public function create(int $orderId, PaymentMethod $method, PaymentStatus $status): int;

    /**
     * Updates a payment's status and optionally records the paid-at timestamp.
     */
    public function updateStatus(int $paymentId, PaymentStatus $status, ?\DateTimeImmutable $paidAtUtc = null): void;

    /**
     * Stores the Stripe Checkout Session ID on the payment record.
     */
    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void;

    /**
     * Stores the Stripe PaymentIntent ID on the payment record.
     */
    public function updateStripePaymentIntentId(int $paymentId, string $stripePaymentIntentId): void;

    /**
     * Stores an external provider reference (e.g. transaction ID) on the payment record.
     */
    public function updateProviderRef(int $paymentId, string $providerRef): void;

    /**
     * Updates payment status only when its current status is in the allowed list.
     * Also clears PaidAtUtc when the new status is not Paid.
     *
     * @param PaymentStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $paymentId, PaymentStatus $newStatus, array $allowedCurrentStatuses): void;
}

