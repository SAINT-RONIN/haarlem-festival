<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

/**
 * Shared mapper that converts CMS content models (hero section, global UI) into
 * arrays and ViewModels reused across all public-facing pages (Home, Jazz, Storytelling, etc.).
 */
final class CmsMapper
{
    /**
     * Flattens HeroData and GlobalUiData into a keyed array that the Twig/PHP templates
     * reference directly for hero text, button links, and navigation labels.
     *
     * @return array{hero_section: array<string, string>, global_ui: array<string, string|bool>}
     */
    public static function toCmsData(HeroData $heroData, GlobalUiData $globalUi): array
    {
        return [
            'hero_section' => [
                'hero_main_title' => $heroData->mainTitle,
                'hero_subtitle' => $heroData->subtitle,
                'hero_button_primary' => $heroData->primaryButtonText,
                'hero_button_primary_link' => $heroData->primaryButtonLink,
                'hero_button_secondary' => $heroData->secondaryButtonText,
                'hero_button_secondary_link' => $heroData->secondaryButtonLink,
                'hero_background_image' => $heroData->backgroundImageUrl,
            ],
            'global_ui' => [
                'site_name' => $globalUi->siteName,
                'nav_home' => $globalUi->navHome,
                'nav_jazz' => $globalUi->navJazz,
                'nav_dance' => $globalUi->navDance,
                'nav_history' => $globalUi->navHistory,
                'nav_restaurant' => $globalUi->navRestaurant,
                'nav_storytelling' => $globalUi->navStorytelling,
                'btn_my_program' => $globalUi->btnMyProgram,
                'login_label' => $globalUi->loginLabel,
                'logout_label' => $globalUi->logoutLabel,
                'label_events_count' => $globalUi->labelEventsCount,
                'label_no_events' => $globalUi->labelNoEvents,
                'btn_explore_template' => $globalUi->btnExploreTemplate,
                'is_logged_in' => $globalUi->isLoggedIn,
            ],
        ];
    }

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
