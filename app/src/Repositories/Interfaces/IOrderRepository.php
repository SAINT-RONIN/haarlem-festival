<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\OrderStatus;

interface IOrderRepository
{
    public function create(
        int $userAccountId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        ?\DateTimeImmutable $payBeforeUtc,
    ): int;

    public function updateStatus(int $orderId, OrderStatus $status): void;
}

