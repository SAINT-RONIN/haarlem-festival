<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\OrderWithDetails;

interface ICmsOrdersService
{
    /**
     * @return OrderWithDetails[]
     */
    public function getOrdersWithDetails(?string $statusFilter = null): array;
}
