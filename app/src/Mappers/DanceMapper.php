<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\DancePageConstants;
use App\Models\DancePageData;
use App\ViewModels\Dance\DancePageViewModel;
use App\ViewModels\Dance\ExperienceData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final class DanceMapper
{
    public static function toPageViewModel(
        DancePageData $data,
        ?ScheduleSectionViewModel $scheduleSection,
        bool $isLoggedIn,
    ): DancePageViewModel {
        $hero = new HeroData(
            mainTitle: $data->heroSection->heroMainTitle ?? 'DANCE! FESTIVAL 2025',
            subtitle: $data->heroSection->heroSubtitle ?? 'Haarlem · 3 Days · Music · Culture',
            primaryButtonText: $data->heroSection->heroButtonPrimary ?? 'Discover all events',
            primaryButtonLink: $data->heroSection->heroButtonPrimaryLink ?? '/dance#headliners',
            secondaryButtonText: $data->heroSection->heroButtonSecondary ?? 'What is Haarlem DANCE! Festival?',
            secondaryButtonLink: $data->heroSection->heroButtonSecondaryLink ?? '/dance#about',
            backgroundImageUrl: $data->heroSection->heroBackgroundImage ?? DancePageConstants::DEFAULT_HERO_BACKGROUND_IMAGE,
            currentPage: DancePageConstants::CURRENT_PAGE,
        );

        $gradient = new GradientSectionData(
            headingText: $data->gradientSection->gradientHeading ?? '',
            subheadingText: $data->gradientSection->gradientSubheading ?? '',
            backgroundImageUrl: $data->gradientSection->gradientBackgroundImage ?? DancePageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE,
        );

        $intro = new IntroSplitSectionData(
            headingText: $data->introSection->introHeading ?? '',
            bodyText: $data->introSection->introBody ?? '',
            imageUrl: $data->introSection->introImage ?? DancePageConstants::DEFAULT_INTRO_IMAGE,
            imageAltText: $data->introSection->introImageAlt ?? DancePageConstants::DEFAULT_INTRO_IMAGE_ALT,
            subsections: null,
            closingLine: null,
        );

        $experience = new ExperienceData(
            title: $data->experienceSection->title ?? 'The Festival Experience',
            imageUrls: $data->experienceSection->imageUrls,
        );

        return new DancePageViewModel(
            heroData: $hero,
            globalUi: GlobalUiMapper::toViewModel($data->globalUiContent, $isLoggedIn),
            cms: [],
            gradientSection: $gradient,
            introSplitSection: $intro,
            experienceData: $experience,
            scheduleSection: $scheduleSection,
        );
    }
}