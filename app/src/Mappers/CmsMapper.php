<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

class CmsMapper
{
    /**
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

    public static function toHeroData(array $content, string $currentPage): HeroData
    {
        return new HeroData(
            mainTitle: $content['hero_main_title'] ?? 'Welcome',
            subtitle: $content['hero_subtitle'] ?? '',
            primaryButtonText: $content['hero_button_primary'] ?? 'Explore',
            primaryButtonLink: $content['hero_button_primary_link'] ?? '#',
            secondaryButtonText: $content['hero_button_secondary'] ?? 'Learn More',
            secondaryButtonLink: $content['hero_button_secondary_link'] ?? '#',
            backgroundImageUrl: $content['hero_background_image'] ?? '/assets/Image/HeroImageHome.png',
            currentPage: $currentPage,
        );
    }

    public static function toGlobalUiData(array $content, bool $isLoggedIn): GlobalUiData
    {
        return new GlobalUiData(
            siteName: $content['site_name'] ?? 'Haarlem Festivals',
            navHome: $content['nav_home'] ?? 'Home',
            navJazz: $content['nav_jazz'] ?? 'Jazz',
            navDance: $content['nav_dance'] ?? 'Dance',
            navHistory: $content['nav_history'] ?? 'History',
            navRestaurant: $content['nav_restaurant'] ?? 'Restaurant',
            navStorytelling: $content['nav_storytelling'] ?? 'Storytelling',
            btnMyProgram: $content['btn_my_program'] ?? 'My Program',
            loginLabel: $content['login_label'] ?? 'Login',
            logoutLabel: $content['logout_label'] ?? 'Logout',
            labelEventsCount: $content['label_events_count'] ?? 'events',
            labelNoEvents: $content['label_no_events'] ?? 'No events scheduled',
            btnExploreTemplate: $content['btn_explore_template'] ?? 'Explore {title} Events',
            isLoggedIn: $isLoggedIn,
        );
    }
}
