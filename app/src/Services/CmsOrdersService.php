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

    /**
     * Returns structured detail data for a single order, or null if the order does not exist.
     *
     * @return array{order: array, items: array, payments: array, tickets: array}|null
     */
    public function getOrderDetail(int $orderId): ?array
    {
        $order = $this->ordersRepository->findOrderById($orderId);
        if ($order === null) {
            return null;
        }

        return [
            'order'    => $order,
            'items'    => $this->ordersRepository->findOrderItems($orderId),
            'payments' => $this->ordersRepository->findOrderPayments($orderId),
            'tickets'  => $this->ordersRepository->findOrderTickets($orderId),
        ];
    }
}
