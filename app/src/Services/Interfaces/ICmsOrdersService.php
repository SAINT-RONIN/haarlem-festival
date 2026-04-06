<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Checkout\OrderWithDetails;
use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;
use App\Models\Invoice;

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

    /**
     * Returns a structured detail result for a single order, or null if the order does not exist.
     *
     * @return array{
     *     order: CmsOrderDetailDto,
     *     items: CmsOrderItemDto[],
     *     payments: CmsOrderPaymentDto[],
     *     tickets: CmsOrderTicketDto[],
     *     invoice: ?Invoice,
     *     invoicePdfPath: ?string
     * }|null
     */
    public function getOrderDetail(int $orderId): ?array;
}
