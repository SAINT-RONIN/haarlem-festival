<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\OrderStatus;

/**
 * Contract for managing rows in the Order table. Orders are created in Pending status
 * during checkout and transition through statuses (Paid, Cancelled, Expired) as payment
 * completes or times out. Supports guarded status transitions for webhook safety.
 */
interface IOrderRepository
{
    /**
     * Creates a new order in Pending status. The payBeforeUtc deadline controls when
     * unpaid orders become eligible for automatic expiration.
     *
     * @param string $subtotal Monetary amounts as strings to preserve decimal precision.
     * @return int The auto-incremented OrderId.
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
     * Unconditionally sets the order status. Use updateStatusIfCurrentIn() when you need
     * to guard against race conditions during concurrent payment callbacks.
     */
    public function updateStatus(int $orderId, OrderStatus $status): void;

    /**
     * Atomically transitions order status only if the current status is in the allowed set.
     * Prevents invalid transitions (e.g. marking a Cancelled order as Paid) when webhooks
     * and expiration jobs race against each other.
     *
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void;
}

