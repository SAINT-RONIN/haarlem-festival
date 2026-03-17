<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingPageViewModel extends BaseViewModel
{
    private const DEFAULT_GRADIENT_HEADING = 'Every story opens a new world.';
    private const DEFAULT_GRADIENT_SUBHEADING = 'Discover voices, moments, and memories in Haarlem.';
    private const DEFAULT_INTRO_HEADING = 'Stories in Haarlem';
    private const DEFAULT_INTRO_BODY = 'Storytelling sessions connect people through culture, humor, and lived experiences.';

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

    public static function fromData(
        array $heroContent,
        string $currentPage,
        array $globalUiContent,
        bool $isLoggedIn,
        array $gradientContent,
        array $introContent,
        array $masonryContent,
        array $scheduleData,
    ): self {
        return new self(
            heroData: HeroData::fromCms($heroContent, $currentPage),
            globalUi: GlobalUiData::fromCms($globalUiContent, $isLoggedIn),
            gradientSection: self::mapGradientSection($gradientContent),
            introSplitSection: self::mapIntroSplitSection($introContent),
            masonrySection: MasonrySectionData::fromCms($masonryContent),
            scheduleSection: ScheduleSectionViewModel::fromData($scheduleData),
        );
    }

    private static function mapGradientSection(array $content): GradientSectionData
    {
        return new GradientSectionData(
            headingText: ImageHelper::getStringValue($content, 'gradient_heading', self::DEFAULT_GRADIENT_HEADING),
            subheadingText: ImageHelper::getStringValue($content, 'gradient_subheading', self::DEFAULT_GRADIENT_SUBHEADING),
            backgroundImageUrl: ImageHelper::validatePath((string)($content['gradient_background_image'] ?? '')),
        );
    }

    private static function mapIntroSplitSection(array $content): IntroSplitSectionData
    {
        $heading = ImageHelper::getStringValue($content, 'intro_heading', self::DEFAULT_INTRO_HEADING);

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: ImageHelper::getStringValue($content, 'intro_body', self::DEFAULT_INTRO_BODY),
            imageUrl: ImageHelper::validatePath((string)($content['intro_image'] ?? '')),
            imageAltText: ImageHelper::getStringValue($content, 'intro_image_alt', $heading),
        );
    }
}
