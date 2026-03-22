<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the HistoricalLocation intro_section.
 */
final readonly class HistoricalLocationIntroContent
{
    public function __construct(
        public ?string $introHeading,
        public ?string $introText,
        public ?string $introFact,
        public ?string $introImage,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            introHeading: $raw['intro_heading'] ?? null,
            introText: $raw['intro_text'] ?? null,
            introFact: $raw['intro_fact'] ?? null,
            introImage: $raw['intro_image'] ?? null,
        );
    }
}
