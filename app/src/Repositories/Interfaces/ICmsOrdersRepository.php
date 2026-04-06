<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Checkout\OrderWithDetails;
use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;

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
    public function findOrderById(int $orderId): ?CmsOrderDetailDto;

    /**
     * Returns all line items for a given order.
     *
     * @return CmsOrderItemDto[]
     */
    public function findOrderItems(int $orderId): array;

    /**
     * Returns all payment records for a given order.
     *
     * @return CmsOrderPaymentDto[]
     */
    public function findOrderPayments(int $orderId): array;

    /**
     * Returns all tickets for a given order.
     *
     * @return CmsOrderTicketDto[]
     */
    public function findOrderTickets(int $orderId): array;
}
