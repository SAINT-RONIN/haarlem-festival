<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;

/**
 * Carries all CMS sections and domain data needed to render the Restaurant listing page.
 */
final readonly class RestaurantPageData
{
    /**
     * @param array<string, ?string> $introSplitContent   Raw CMS items for the intro section
     * @param array<string, ?string> $introSplit2Content   Raw CMS items for the second intro section
     * @param array<string, ?string> $instructionsContent  Raw CMS items for the instructions section
     * @param array<string, ?string> $cardsContent         Raw CMS items for the cards section
     * @param \App\Models\Restaurant[] $restaurants        Filtered restaurant domain objects to display
     * @param string[] $allCuisines                        All available cuisine filter labels (always starts with "All")
     */
    public function __construct(
        public HeroSectionContent $heroContent,
        public GlobalUiContent $globalUiContent,
        public GradientSectionContent $gradientSection,
        public array $introSplitContent,
        public array $introSplit2Content,
        public array $instructionsContent,
        public array $cardsContent,
        public array $restaurants,
        public array $allCuisines,
    ) {}
}
