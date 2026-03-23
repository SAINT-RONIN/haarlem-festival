<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS orders list page.
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
