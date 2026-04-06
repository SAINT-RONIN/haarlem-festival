<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;

/**
 * Maps raw CMS arrays into shared/global content models.
 *
 * Handles GlobalUiContent, HeroSectionContent, GradientSectionContent,
 * and IntroSectionContent — all used across multiple page services.
 */
final class GlobalContentMapper
{
    /** Maps raw CMS data to a GlobalUiContent model. */
    public static function mapGlobalUi(array $raw): GlobalUiContent
    {
        return new GlobalUiContent(
            siteName: $raw['site_name'] ?? null,
            navHome: $raw['nav_home'] ?? null,
            navJazz: $raw['nav_jazz'] ?? null,
            navDance: $raw['nav_dance'] ?? null,
            navHistory: $raw['nav_history'] ?? null,
            navRestaurant: $raw['nav_restaurant'] ?? null,
            navStorytelling: $raw['nav_storytelling'] ?? null,
            btnMyProgram: $raw['btn_my_program'] ?? null,
            loginLabel: $raw['login_label'] ?? null,
            logoutLabel: $raw['logout_label'] ?? null,
            labelEventsCount: $raw['label_events_count'] ?? null,
            labelNoEvents: $raw['label_no_events'] ?? null,
            btnExploreTemplate: $raw['btn_explore_template'] ?? null,
        );
    }

    /** Maps raw CMS data to a HeroSectionContent model. */
    public static function mapHero(array $raw): HeroSectionContent
    {
        return new HeroSectionContent(
            heroMainTitle: $raw['hero_main_title'] ?? null,
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroButtonPrimary: $raw['hero_button_primary'] ?? null,
            heroButtonPrimaryLink: $raw['hero_button_primary_link'] ?? null,
            heroButtonSecondary: $raw['hero_button_secondary'] ?? null,
            heroButtonSecondaryLink: $raw['hero_button_secondary_link'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
        );
    }

    /** Maps raw CMS data to a GradientSectionContent model. */
    public static function mapGradient(array $raw): GradientSectionContent
    {
        return new GradientSectionContent(
            gradientHeading: $raw['gradient_heading'] ?? null,
            gradientSubheading: $raw['gradient_subheading'] ?? null,
            gradientBackgroundImage: $raw['gradient_background_image'] ?? null,
        );
    }

    /** Maps raw CMS data to an IntroSectionContent model. */
    public static function mapIntro(array $raw): IntroSectionContent
    {
        return new IntroSectionContent(
            introHeading: $raw['intro_heading'] ?? null,
            introBody: $raw['intro_body'] ?? null,
            introImage: $raw['intro_image'] ?? null,
            introImageAlt: $raw['intro_image_alt'] ?? null,
        );
    }
}
