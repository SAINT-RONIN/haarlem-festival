<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

interface IPaymentRepository
{
    public function create(int $orderId, PaymentMethod $method, PaymentStatus $status): int;

    public function updateStatus(int $paymentId, PaymentStatus $status, ?\DateTimeImmutable $paidAtUtc = null): void;

    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void;

    public function updateStripePaymentIntentId(int $paymentId, string $stripePaymentIntentId): void;

    public function updateProviderRef(int $paymentId, string $providerRef): void;

    /**
     * Updates payment status only when its current status is in the allowed list.
     * Also clears PaidAtUtc when the new status is not Paid.
     *
     * @param PaymentStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $paymentId, PaymentStatus $newStatus, array $allowedCurrentStatuses): void;
}

