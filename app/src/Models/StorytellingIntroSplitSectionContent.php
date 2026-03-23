<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for the storytelling page intro split-layout section.
 * Hydrated from CMS key-value pairs.
 */
final readonly class StorytellingIntroSplitSectionContent
{
    public function __construct(
        public ?string $introHeading,
        public ?string $introBody,
        public ?string $introImage,
        public ?string $introImageAlt,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            introHeading: $raw['intro_heading'] ?? null,
            introBody: $raw['intro_body'] ?? null,
            introImage: $raw['intro_image'] ?? null,
            introImageAlt: $raw['intro_image_alt'] ?? null,
        );
    }
}
