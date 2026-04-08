<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * Hero banner data for public pages — title, subtitle, background image, and CTA button.
 */
final readonly class HeroData
{
    public function __construct(
        public string $mainTitle,
        public string $subtitle,
        public string $primaryButtonText,
        public string $primaryButtonLink,
        public string $secondaryButtonText,
        public string $secondaryButtonLink,
        public string $backgroundImageUrl,
        public string $currentPage,
    ) {}
}
