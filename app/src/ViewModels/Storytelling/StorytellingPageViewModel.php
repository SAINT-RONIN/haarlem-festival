<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the Storytelling page.
 */
final readonly class StorytellingPageViewModel
{
    public function __construct(
        public HeroData                 $heroData,
        public GlobalUiData             $globalUi,
        public GradientSectionData      $gradientSection,
        public IntroSplitSectionData    $introSplitSection,
        public MasonrySectionData       $masonrySection,
        public ScheduleSectionViewModel $scheduleSection,
    )
    {
    }
}

