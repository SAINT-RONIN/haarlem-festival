<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * ViewModel for the Restaurant page.
 */
final readonly class RestaurantPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public ?IntroSplitSectionData $introSplit2Section = null,
        public ?InstructionsSectionData $instructionsSection = null,
        public ?RestaurantCardsSectionData $restaurantCardsSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            cms: $cms,
            includeNav: false,
        );
    }
}
