<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * Carries display-ready data for the custom hero overlay on a historical location page.
 * The reason for this is because the detail hero includes inline navigation, a map, and action buttons
 * that do not exist on the standard HeroData, so it needs its own typed container.
 */
final readonly class LocationHero
{
    public function __construct(
        public string $mainTitle,
        public string $subtitle,
        public string $buttonText,
        public string $buttonLink,
        public string $backgroundImageUrl,
        public string $currentPage,
        public string $mapImageUrl,
    ) {}
}
