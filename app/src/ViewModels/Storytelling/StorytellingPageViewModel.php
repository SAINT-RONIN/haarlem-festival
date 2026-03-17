<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Constants\StorytellingPageConstants;
use App\Helpers\ImageHelper;
use App\Models\StorytellingPageData;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public MasonrySectionData $masonrySection,
        public ScheduleSectionViewModel $scheduleSection,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }

    /**
     * @param array{globalUiContent: array<string, mixed>, isLoggedIn: bool} $sharedData
     */
    public static function fromDomainData(StorytellingPageData $pageData, array $sharedData): self
    {
        $sections = $pageData->sections;
        $globalUiContent = $sharedData['globalUiContent'] ?? [];
        $isLoggedIn = (bool)($sharedData['isLoggedIn'] ?? false);

        return new self(
            heroData: self::buildHeroData($sections),
            globalUi: GlobalUiData::fromCms($globalUiContent, $isLoggedIn),
            gradientSection: self::buildGradientSection($sections),
            introSplitSection: self::buildIntroSplitSection($sections),
            masonrySection: MasonrySectionData::fromCms($sections[StorytellingPageConstants::SECTION_MASONRY] ?? []),
            scheduleSection: ScheduleSectionViewModel::fromData($pageData->scheduleSectionData),
        );
    }

    /** @param array<string, mixed> $sections */
    private static function buildHeroData(array $sections): HeroData
    {
        $section = $sections[StorytellingPageConstants::SECTION_HERO] ?? [];
        return HeroData::fromCms($section, StorytellingPageConstants::CURRENT_PAGE);
    }

    /** @param array<string, mixed> $sections */
    private static function buildGradientSection(array $sections): GradientSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_GRADIENT] ?? [];
        return new GradientSectionData(
            headingText: ImageHelper::getStringValue($section, 'gradient_heading', 'Every story opens a new world.'),
            subheadingText: ImageHelper::getStringValue($section, 'gradient_subheading', 'Discover voices, moments, and memories in Haarlem.'),
            backgroundImageUrl: ImageHelper::validatePath((string)($section['gradient_background_image'] ?? '')),
        );
    }

    /** @param array<string, mixed> $sections */
    private static function buildIntroSplitSection(array $sections): IntroSplitSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_INTRO_SPLIT] ?? [];
        $heading = ImageHelper::getStringValue($section, 'intro_heading', 'Stories in Haarlem');
        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: ImageHelper::getStringValue($section, 'intro_body', 'Storytelling sessions connect people through culture, humor, and lived experiences.'),
            imageUrl: ImageHelper::validatePath((string)($section['intro_image'] ?? '')),
            imageAltText: ImageHelper::getStringValue($section, 'intro_image_alt', $heading),
        );
    }
}
