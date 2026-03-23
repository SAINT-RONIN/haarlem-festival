<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\OrderStatus;

/**
 * Defines persistence operations for customer orders.
 */
interface IOrderRepository
{
    /**
     * Inserts a new order linked to a user and program, and returns the generated order ID.
     */
    public function create(
        int $userAccountId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        ?\DateTimeImmutable $payBeforeUtc,
    ): int;

    /**
     * Unconditionally updates an order's status.
     */
    public function updateStatus(int $orderId, OrderStatus $status): void;

    /**
     * Updates order status only when its current status is in the allowed list.
     *
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void;
}

