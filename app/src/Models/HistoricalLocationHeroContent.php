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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            heroMainTitle: $raw['hero_main_title'] ?? null,
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroButton: $raw['hero_button'] ?? null,
            heroButtonLink: $raw['hero_button_link'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
            heroMapImage: $raw['hero_map_image'] ?? null,
        );
    }
}
