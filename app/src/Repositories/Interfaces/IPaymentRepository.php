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
}

