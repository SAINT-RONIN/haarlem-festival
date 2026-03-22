<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Restaurant gradient_section.
 */
final readonly class RestaurantGradientSectionContent
{
    public function __construct(
        public ?string $gradientHeading,
        public ?string $gradientSubheading,
        public ?string $gradientBackgroundImage,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            gradientHeading: $raw['gradient_heading'] ?? null,
            gradientSubheading: $raw['gradient_subheading'] ?? null,
            gradientBackgroundImage: $raw['gradient_background_image'] ?? null,
        );
    }
}
