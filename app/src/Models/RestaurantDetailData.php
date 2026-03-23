<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries all data needed to render the Restaurant detail page.
 */
final readonly class RestaurantDetailData
{
    /**
     * @param array<mixed> $timeSlots
     * @param array<mixed> $priceCards
     * @param RestaurantImage[] $images
     */
    public function __construct(
        public Restaurant $restaurant,
        public RestaurantDetailSectionContent $cms,
        public GlobalUiContent $globalUiContent,
        public array $timeSlots,
        public array $priceCards,
        public array $images = [],
    ) {}
}
