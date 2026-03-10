<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * ViewModel for the Dance page.
 */
final readonly class DancePageViewModel
{
    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public ExperienceData $experienceData,
    ) {
    }
}


