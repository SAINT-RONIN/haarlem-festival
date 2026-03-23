<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderWithDetails;
use App\Repositories\Interfaces\ICmsOrdersRepository;
use App\Services\Interfaces\ICmsOrdersService;

/**
 * Service for the CMS Orders list page.
 */
class CmsOrdersService implements ICmsOrdersService
{
    public function __construct(
        private readonly ICmsOrdersRepository $ordersRepository,
    ) {
    }

    /**
     * Returns all orders with user email, item summary, and latest payment status.
     * Optionally filtered by order status.
     *
     * @return OrderWithDetails[]
     */
    public function getOrdersWithDetails(?string $statusFilter = null): array
    {
        return $this->ordersRepository->findOrdersWithDetails($statusFilter);
    }
}
