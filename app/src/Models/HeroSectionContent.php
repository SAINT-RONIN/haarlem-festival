<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for any page hero section.
 * Used by CmsMapper::toHeroData() instead of a raw array.
 */
final readonly class HeroSectionContent
{
    public function __construct(
        public ?string $heroMainTitle,
        public ?string $heroSubtitle,
        public ?string $heroButtonPrimary,
        public ?string $heroButtonPrimaryLink,
        public ?string $heroButtonSecondary,
        public ?string $heroButtonSecondaryLink,
        public ?string $heroBackgroundImage,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            heroMainTitle: $raw['hero_main_title'] ?? null,
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroButtonPrimary: $raw['hero_button_primary'] ?? null,
            heroButtonPrimaryLink: $raw['hero_button_primary_link'] ?? null,
            heroButtonSecondary: $raw['hero_button_secondary'] ?? null,
            heroButtonSecondaryLink: $raw['hero_button_secondary_link'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
        );
    }
}
