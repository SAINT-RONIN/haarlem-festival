<?php

declare(strict_types=1);

namespace App\Content;

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
    ) {
    }
}
