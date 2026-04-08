<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

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
}
