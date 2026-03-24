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
     */
    public function __construct(
        public array   $orders,
        public string  $selectedStatus,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}
