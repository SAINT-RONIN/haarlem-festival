<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Content\GlobalUiContent;
use App\Content\GradientSectionContent;
use App\Content\HeroSectionContent;
use App\Content\RestaurantCardsSectionContent;
use App\Content\RestaurantInstructionsSectionContent;
use App\Content\RestaurantIntroSectionContent;
use App\Content\RestaurantIntroSplit2SectionContent;

/**
 * Carries all CMS sections and domain data needed to render the Restaurant listing page.
 *
 * @param RestaurantListingData[] $listings Event-based restaurant listing cards
 */
final readonly class RestaurantPageData
{
    /**
     * @param \App\Models\Restaurant[] $restaurants
     * @param array<int, \App\Models\CuisineType[]> $cuisinesByRestaurant
     * @param RestaurantListingData[] $listings
     */
    public function __construct(
        public HeroSectionContent $heroContent,
        public GlobalUiContent $globalUiContent,
        public GradientSectionContent $gradientSection,
        public RestaurantIntroSectionContent $introSplitSection,
        public RestaurantIntroSplit2SectionContent $introSplit2Section,
        public RestaurantInstructionsSectionContent $instructionsSection,
        public RestaurantCardsSectionContent $cardsSection,
        public array $restaurants,
        public array $cuisinesByRestaurant,
        public array $listings = [],
        public ?string $activeFilter = null,
    ) {}
}
