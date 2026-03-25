<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

/**
 * Carries all data needed to render the Restaurant detail page.
 */
final readonly class RestaurantDetailData
{
    /**
     * @param array<string, string[]> $imagesByType
     * @param array<mixed> $timeSlots
     * @param array<mixed> $priceCards
     * @param CuisineType[] $cuisineTypes
     */
    public function __construct(
        public Restaurant $restaurant,
        public array $imagesByType,
        public RestaurantDetailSectionContent $cms,
        public GlobalUiContent $globalUiContent,
        public array $timeSlots,
        public array $priceCards,
        public array $cuisineTypes,
    ) {}
}
