<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the Storytelling page.
 */
final readonly class StorytellingPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData      $gradientSection,
        public IntroSplitSectionData    $introSplitSection,
        public MasonrySectionData       $masonrySection,
        public ScheduleSectionViewModel $scheduleSection,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }
}
