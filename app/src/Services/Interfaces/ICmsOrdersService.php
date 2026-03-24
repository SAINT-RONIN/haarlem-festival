<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\OrderWithDetails;

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
}
