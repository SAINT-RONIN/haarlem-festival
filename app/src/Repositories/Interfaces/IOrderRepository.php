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

    /**
     * Updates order status only when its current status is in the allowed list.
     *
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void;
}

