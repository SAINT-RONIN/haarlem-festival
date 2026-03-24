<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Carries all display-ready data for a single Storytelling event detail page view.
 * The reason for this is because the detail page has more sections than the overview page, each needing its own sub-ViewModel, and grouping them here gives the view one predictable place to find everything.
 */
final readonly class StorytellingDetailPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,
        string $currentPage,
        public StorytellingDetailHeroData $detailHero,
        public StorytellingAboutSectionData $aboutSection,
        public StoryHighlightsSectionData $highlightsSection,
        public StoryGallerySectionData $gallerySection,
        public StoryVideoSectionData $videoSection,
        public ScheduleSectionViewModel $scheduleSection,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $currentPage,
            cms: $cms,
            includeNav: false,
        );
    }
}
