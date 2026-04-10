<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Domain\Checkout\OrderWithDetails;
use App\DTOs\Cms\CmsOrderDetailData;
use App\DTOs\Cms\CmsOrderItemData;
use App\DTOs\Cms\CmsOrderPaymentData;
use App\DTOs\Cms\CmsOrderTicketData;

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

    /**
     * Returns a single order with user/recipient details, or null if not found.
     */
    public function findOrderById(int $orderId): ?CmsOrderDetailData;

    /**
     * Returns all line items for a given order.
     *
     * @return CmsOrderItemData[]
     */
    public function findOrderItems(int $orderId): array;

    /**
     * Returns all payment records for a given order.
     *
     * @return CmsOrderPaymentData[]
     */
    public function findOrderPayments(int $orderId): array;

    /**
     * Returns all tickets for a given order.
     *
     * @return CmsOrderTicketData[]
     */
    public function findOrderTickets(int $orderId): array;
}
