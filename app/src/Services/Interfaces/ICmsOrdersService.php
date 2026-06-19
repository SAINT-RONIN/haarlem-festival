<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\CmsOrderDetailPageData;
use App\DTOs\Cms\CmsOrdersFilter;
use App\DTOs\Domain\Checkout\OrderWithDetails;

/**
 * Defines the contract for CMS order listing and filtering.
 */
interface ICmsOrdersService
{
    /**
     * Orders matching the filter. $limit null = every matching row (export);
     * set = a single page slice (list view).
     *
     * @return OrderWithDetails[]
     */
    public function getOrders(CmsOrdersFilter $filter, ?int $limit = null, ?int $offset = null): array;

    /** Total orders matching the filter, for pagination. */
    public function countOrders(CmsOrdersFilter $filter): int;

    public function getOrderDetail(int $orderId): ?CmsOrderDetailPageData;
}
