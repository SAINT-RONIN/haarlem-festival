<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\OrderWithDetails;

/**
 * Defines read-only queries for retrieving orders with joined details for CMS display.
 */
interface ICmsOrdersRepository
{
    /**
     * Returns all orders with joined details, optionally filtered by status.
     *
     * @return OrderWithDetails[]
     */
    public function findOrdersWithDetails(?string $statusFilter = null): array;
}
