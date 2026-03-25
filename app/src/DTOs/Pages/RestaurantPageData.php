<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

/**
 * Carries all CMS sections and domain data needed to render the Restaurant listing page.
 */
final readonly class RestaurantPageData
{
    /**
     * @param Restaurant[] $restaurants
     * @param array<int, CuisineType[]> $cuisinesByRestaurant
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
    ) {}
}
