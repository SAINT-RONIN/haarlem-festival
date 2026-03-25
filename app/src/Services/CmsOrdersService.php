<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Checkout\OrderWithDetails;
use App\Repositories\Interfaces\ICmsOrdersRepository;
use App\Services\Interfaces\ICmsOrdersService;

/**
 * CMS-side order management: read-only listing with joined user, item, and payment data.
 *
 * Delegates entirely to the orders repository; exists so controllers depend on a
 * service interface rather than a repository directly.
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
