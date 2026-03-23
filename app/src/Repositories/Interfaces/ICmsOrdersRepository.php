<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\OrderWithDetails;

interface ICmsOrdersRepository
{
    /**
     * Returns all orders with joined details, optionally filtered by status.
     *
     * @return OrderWithDetails[]
     */
    public function findOrdersWithDetails(?string $statusFilter = null): array;
}
