<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Checkout\OrderWithDetails;

/**
 * Defines the contract for CMS order listing and filtering.
 */
interface ICmsOrdersService
{
    /**
     * Returns all orders with their associated details, optionally filtered by payment/order status.
     *
     * @return OrderWithDetails[]
     */
    public function getOrdersWithDetails(?string $statusFilter = null): array;

    /**
     * Returns a structured detail result for a single order, or null if the order does not exist.
     *
     * @return array{order: array, items: array, payments: array, tickets: array}|null
     */
    public function getOrderDetail(int $orderId): ?array;
}
