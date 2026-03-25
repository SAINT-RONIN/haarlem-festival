<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the HistoricalLocation hero section (location-specific hero fields).
 */
final readonly class HistoricalLocationHeroContent
{
    public function __construct(
        public ?string $heroMainTitle,
        public ?string $heroSubtitle,
        public ?string $heroButton,
        public ?string $heroButtonLink,
        public ?string $heroBackgroundImage,
        public ?string $heroMapImage,
    ) {}
}
