<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS orders list page (orders.php).
 *
 * Carries order items with status badges and filter state.
 */
final readonly class CmsOrdersListViewModel
{
    /**
     * @param CmsOrderListItemViewModel[] $orders
     * @param string[] $statusOptions All valid order status values for the filter dropdown
     */
    public function __construct(
        public array   $orders,
        public array   $statusOptions,
        public string  $selectedStatus,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}
