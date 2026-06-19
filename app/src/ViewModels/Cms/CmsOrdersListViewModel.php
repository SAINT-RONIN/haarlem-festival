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
     * @param CmsOrderListItemViewModel[] $orders Orders on the current page only
     * @param string[] $statusOptions All valid order status values for the filter dropdown
     * @param string $fromDate Selected range start ('Y-m-d')
     * @param string $toDate Selected range end ('Y-m-d')
     * @param int $currentPage 1-based page currently shown
     * @param int $totalPages Total number of pages for the current filter
     * @param int $totalCount Total orders matching the current filter (across all pages)
     */
    public function __construct(
        public array   $orders,
        public array   $statusOptions,
        public string  $selectedStatus,
        public string  $fromDate,
        public string  $toDate,
        public int     $currentPage,
        public int     $totalPages,
        public int     $totalCount,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}
