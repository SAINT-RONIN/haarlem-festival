<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;

/**
 * Carries all CMS sections and domain data needed to render the Restaurant listing page.
 */
final readonly class RestaurantPageData
{
    /**
     * @param RestaurantListingData[] $listings Event-based restaurant listing cards
     */
    public function __construct(
        public HeroSectionContent $heroContent,
        public GlobalUiContent $globalUiContent,
        public GradientSectionContent $gradientSection,
        public RestaurantIntroSectionContent $introSplitSection,
        public RestaurantIntroSplit2SectionContent $introSplit2Section,
        public RestaurantInstructionsSectionContent $instructionsSection,
        public RestaurantCardsSectionContent $cardsSection,
        public array $listings,
    ) {}
}
