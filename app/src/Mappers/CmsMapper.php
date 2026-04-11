<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * Shared mapper that converts CMS content models into reusable public-page ViewModels.
 */
final class CmsMapper
{
    /**
     * Converts a HeroSectionContent model into a HeroData ViewModel with safe defaults
     * for every field, ensuring the hero partial always has renderable values.
     */
    public static function toHeroData(HeroSectionContent $content, string $currentPage): HeroData
    {
        return new HeroData(
            mainTitle: $content->heroMainTitle ?? 'Welcome',
            subtitle: $content->heroSubtitle ?? '',
            primaryButtonText: $content->heroButtonPrimary ?? 'Explore',
            primaryButtonLink: $content->heroButtonPrimaryLink ?? '#',
            secondaryButtonText: $content->heroButtonSecondary ?? 'Learn More',
            secondaryButtonLink: $content->heroButtonSecondaryLink ?? '#',
            backgroundImageUrl: $content->heroBackgroundImage ?? '/assets/Image/HeroImageHome.png',
            currentPage: $currentPage,
        );
    }

    /**
     * Converts a GradientSectionContent model into a GradientSectionData ViewModel.
     * Shared by Jazz, History, and Storytelling pages.
     */
    public static function toGradientSection(GradientSectionContent $content, string $defaultBgImage = ''): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $content->gradientHeading ?? '',
            subheadingText: $content->gradientSubheading ?? '',
            backgroundImageUrl: $content->gradientBackgroundImage ?? $defaultBgImage,
        );
    }

    /**
     * Converts an IntroSectionContent model into an IntroSplitSectionData ViewModel.
     * Shared by Jazz and History pages. Restaurant uses its own variant.
     */
    public static function toIntroSplitSection(IntroSectionContent $content, string $defaultImage = '', string $defaultAlt = ''): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText: $content->introHeading ?? '',
            bodyText: $content->introBody ?? '',
            imageUrl: $content->introImage ?? $defaultImage,
            imageAltText: $content->introImageAlt ?? $defaultAlt,
            label: $content->introLabel ?? null,
        );
    }

    /**
     * Converts a GlobalUiContent model into a GlobalUiData ViewModel containing site name,
     * navigation labels, and button text. Defaults guarantee the shell renders even if CMS rows are missing.
     */
    public static function toGlobalUiData(GlobalUiContent $content, bool $isLoggedIn): GlobalUiData
    {
        return new GlobalUiData(
            siteName: $content->siteName ?? 'Haarlem Festivals',
            navHome: $content->navHome ?? 'Home',
            navJazz: $content->navJazz ?? 'Jazz',
            navDance: $content->navDance ?? 'Dance',
            navHistory: $content->navHistory ?? 'History',
            navRestaurant: $content->navRestaurant ?? 'Restaurant',
            navStorytelling: $content->navStorytelling ?? 'Storytelling',
            btnMyProgram: $content->btnMyProgram ?? 'My Program',
            loginLabel: $content->loginLabel ?? 'Login',
            logoutLabel: $content->logoutLabel ?? 'Logout',
            labelEventsCount: $content->labelEventsCount ?? 'events',
            labelNoEvents: $content->labelNoEvents ?? 'No events scheduled',
            btnExploreTemplate: $content->btnExploreTemplate ?? 'Explore {title} Events',
            isLoggedIn: $isLoggedIn,
        );
    }
}
