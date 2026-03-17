<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Constants\StorytellingDetailConstants;
use App\Constants\StorytellingPageConstants;
use App\Models\StorytellingDetailPageData;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingDetailPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
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
            includeNav: false,
        );
    }

    public static function fromDomainData(StorytellingDetailPageData $pageData, GlobalUiData $globalUi): self
    {
        $scheduleSection = ScheduleSectionViewModel::fromData($pageData->scheduleSectionData);

        $detailHero = StorytellingDetailHeroData::fromData(
            title: $pageData->event->title,
            subtitle: $pageData->event->shortDescription,
            featuredImagePath: $pageData->featuredImagePath,
            labels: $pageData->labels,
            cms: $pageData->cms,
            globalUi: $globalUi,
            currentPage: StorytellingPageConstants::CURRENT_PAGE,
            reserveButtonUrl: '#' . $scheduleSection->sectionId,
        );

        return new self(
            heroData: self::buildShellHero($detailHero),
            globalUi: $globalUi,
            detailHero: $detailHero,
            aboutSection: StorytellingAboutSectionData::fromData(
                fallbackHeading: $pageData->event->title,
                aboutBodyHtml: $pageData->aboutBody,
                cms: $pageData->cms,
            ),
            highlightsSection: StoryHighlightsSectionData::fromCms($pageData->cms),
            gallerySection: StoryGallerySectionData::fromCms($pageData->cms),
            videoSection: StoryVideoSectionData::fromCms($pageData->cms),
            scheduleSection: $scheduleSection,
        );
    }

    private static function buildShellHero(StorytellingDetailHeroData $detailHero): HeroData
    {
        return new HeroData(
            mainTitle: $detailHero->title,
            subtitle: $detailHero->subtitle,
            primaryButtonText: '',
            primaryButtonLink: '#',
            secondaryButtonText: '',
            secondaryButtonLink: '#',
            backgroundImageUrl: $detailHero->heroImageUrl,
            currentPage: StorytellingPageConstants::CURRENT_PAGE,
        );
    }
}
