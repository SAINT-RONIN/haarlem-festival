<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Constants\StorytellingPageConstants;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingDetailPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,
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
            currentPage: StorytellingPageConstants::CURRENT_PAGE,
            cms: $cms,
            includeNav: false,
        );
    }
}
