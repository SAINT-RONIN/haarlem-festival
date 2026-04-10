<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the customer-facing "My Orders" page.
 * Carries a list of order summary cards and shared layout flags.
 */
final readonly class MyOrdersViewModel
{
    public function __construct(
        /** @var MyOrdersItemViewModel[] */
        public array $orders,
        public bool $isLoggedIn,
        public string $currentPage = 'my-orders',
    ) {}
}
