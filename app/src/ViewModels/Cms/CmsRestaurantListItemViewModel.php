<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsRestaurantListItemViewModel
{
    public function __construct(
        public int    $restaurantId,
        public string $name,
        public string $cuisineType,
        public string $city,
        public bool   $isActive,
        public string $createdAt,
    ) {}
}
