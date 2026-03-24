<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsRestaurantsListViewModel
{
    /**
     * @param CmsRestaurantListItemViewModel[] $items
     */
    public function __construct(
        public array   $items,
        public string  $searchQuery,
        public ?string $successMessage,
        public ?string $errorMessage,
        public string  $deleteCsrfToken,
    ) {}
}
