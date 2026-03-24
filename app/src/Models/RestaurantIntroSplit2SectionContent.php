<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Restaurant intro_split2_section.
 */
final readonly class RestaurantIntroSplit2SectionContent
{
    public function __construct(
        public ?string $intro2Heading,
        public ?string $intro2Body,
        public ?string $intro2Image,
        public ?string $intro2ImageAlt,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            intro2Heading: $raw['intro2_heading'] ?? null,
            intro2Body: $raw['intro2_body'] ?? null,
            intro2Image: $raw['intro2_image'] ?? null,
            intro2ImageAlt: $raw['intro2_image_alt'] ?? null,
        );
    }
}
