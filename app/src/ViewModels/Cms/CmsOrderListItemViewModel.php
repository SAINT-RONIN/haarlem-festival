<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a single order row in the CMS orders list.
 *
 * All values are display-ready. Created by CmsOrdersMapper.
 */
final readonly class CmsOrderListItemViewModel
{
    public function __construct(
        public int    $orderId,
        public string $orderNumber,
        public int    $userAccountId,
        public string $userEmail,
        public string $itemsSummary,
        public string $orderStatus,
        public string $paymentStatus,
        public string $totalAmount,
        public string $createdAt,
        public string $statusBadgeClass,
        public string $paymentBadgeClass,
    ) {}
}
