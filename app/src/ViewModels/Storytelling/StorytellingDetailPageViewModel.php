<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingDetailPageViewModel extends BaseViewModel
{
    private const CURRENT_PAGE = 'storytelling';

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
            currentPage: self::CURRENT_PAGE,
            includeNav: false,
        );
    }

    /**
     * @param string[] $labels
     */
    public static function fromEventData(
        array $globalUiContent,
        bool $isLoggedIn,
        string $eventTitle,
        string $eventSubtitle,
        ?string $featuredImagePath,
        array $labels,
        string $aboutBodyHtml,
        array $cms,
        array $scheduleData,
    ): self {
        $globalUi = GlobalUiData::fromCms($globalUiContent, $isLoggedIn);
        $scheduleSection = ScheduleSectionViewModel::fromData($scheduleData);

        $detailHero = StorytellingDetailHeroData::fromData(
            title: $eventTitle,
            subtitle: $eventSubtitle,
            featuredImagePath: $featuredImagePath,
            labels: $labels,
            cms: $cms,
            globalUi: $globalUi,
            currentPage: self::CURRENT_PAGE,
            reserveButtonUrl: '#' . $scheduleSection->sectionId,
        );

        return new self(
            heroData: self::shellHeroFrom($detailHero),
            globalUi: $globalUi,
            detailHero: $detailHero,
            aboutSection: StorytellingAboutSectionData::fromData($eventTitle, $aboutBodyHtml, $cms),
            highlightsSection: StoryHighlightsSectionData::fromCms($cms),
            gallerySection: StoryGallerySectionData::fromCms($cms),
            videoSection: StoryVideoSectionData::fromCms($cms),
            scheduleSection: $scheduleSection,
        );
    }

    // BaseViewModel requires a HeroData, but the detail page renders its own custom hero.
    // Derives shell metadata from the already-built detailHero to avoid duplicating validation.
    private static function shellHeroFrom(StorytellingDetailHeroData $detailHero): HeroData
    {
        return new HeroData(
            mainTitle: $detailHero->title,
            subtitle: $detailHero->subtitle,
            primaryButtonText: '',
            primaryButtonLink: '#',
            secondaryButtonText: '',
            secondaryButtonLink: '#',
            backgroundImageUrl: $detailHero->heroImageUrl,
            currentPage: self::CURRENT_PAGE,
        );
    }
}
