<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for the global_ui section.
 * Used by navigation, buttons, and labels across all pages.
 */
final readonly class GlobalUiContent
{
    public function __construct(
        public ?string $siteName,
        public ?string $navHome,
        public ?string $navJazz,
        public ?string $navDance,
        public ?string $navHistory,
        public ?string $navRestaurant,
        public ?string $navStorytelling,
        public ?string $btnMyProgram,
        public ?string $loginLabel,
        public ?string $logoutLabel,
        public ?string $labelEventsCount,
        public ?string $labelNoEvents,
        public ?string $btnExploreTemplate,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
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
}
