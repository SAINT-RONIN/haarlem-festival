<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the Storytelling page.
 *
 * Contains all pre-formatted data needed by the storytelling page view.
 * The service prepares this data so the view only needs to render.
 */
final readonly class StorytellingPageViewModel
{
    public function __construct(
        public HeroData              $heroData,
        public GlobalUiData          $globalUi,
        public GradientSectionData   $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public MasonrySectionData    $masonrySection,
    )
    {
    }
}

