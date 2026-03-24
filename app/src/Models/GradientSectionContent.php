<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for a page's gradient banner section.
 * Shared across Jazz, Storytelling, and other pages that use the same gradient layout.
 * Hydrated from CMS key-value pairs.
 */
final readonly class GradientSectionContent
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
