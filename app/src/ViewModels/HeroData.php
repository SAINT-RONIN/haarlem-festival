<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for hero section data.
 *
 * All fields are guaranteed to be populated by the Service layer.
 * Views should render without conditionals.
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
    ) {
    }
}
