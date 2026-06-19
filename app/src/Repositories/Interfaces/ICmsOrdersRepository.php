<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Domain\Checkout\OrderWithDetails;
use App\DTOs\Cms\CmsOrderDetailData;
use App\DTOs\Cms\CmsOrderItemData;
use App\DTOs\Cms\CmsOrderPaymentData;
use App\DTOs\Cms\CmsOrderTicketData;
use App\DTOs\Cms\CmsOrdersFilter;

/**
 * Defines read-only queries for retrieving orders with joined details for CMS display.
 */
interface ICmsOrdersRepository
{
    /**
     * Returns orders with joined details matching the filter, newest first.
     *
     * When $limit is null every matching row is returned (used by the export);
     * when set, only that page slice is returned (used by the list view).
     *
     * @return OrderWithDetails[]
     */
    public function findOrders(CmsOrdersFilter $filter, ?int $limit = null, ?int $offset = null): array;

    /** Returns the total number of orders matching the filter (for pagination). */
    public function countOrders(CmsOrdersFilter $filter): int;

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
