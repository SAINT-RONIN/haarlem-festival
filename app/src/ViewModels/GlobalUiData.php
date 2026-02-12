<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for global UI elements (navigation, branding).
 *
 * All fields are guaranteed to be populated by the Service layer.
 * Views should render without conditionals.
 */
final readonly class GlobalUiData
{
    public function __construct(
        public string $siteName,
        public string $navHome,
        public string $navJazz,
        public string $navDance,
        public string $navHistory,
        public string $navRestaurant,
        public string $navStorytelling,
        public string $btnMyProgram,
        public bool   $isLoggedIn = false,
    )
    {
    }
}

