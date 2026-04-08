<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\CmsOrderDetailBundle;
use App\DTOs\Domain\Checkout\OrderWithDetails;

/**
 * Defines the contract for CMS order listing and filtering.
 */
interface ICmsOrdersService
{
    /** @return OrderWithDetails[] */
    public function getOrdersWithDetails(?string $statusFilter = null): array;

    public function getOrderDetail(int $orderId): ?CmsOrderDetailBundle;
}
