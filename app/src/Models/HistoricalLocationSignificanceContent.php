<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the HistoricalLocation significance_section.
 */
final readonly class HistoricalLocationSignificanceContent
{
    public function __construct(
        public ?string $architecturalSignificanceHeading,
        public ?string $architecturalSignificanceText,
        public ?string $historicalSignificanceHeading,
        public ?string $historicalSignificanceText,
        public ?string $significanceImage,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            architecturalSignificanceHeading: $raw['architectural_significance_heading'] ?? null,
            architecturalSignificanceText: $raw['architectural_significance_text'] ?? null,
            historicalSignificanceHeading: $raw['historical_significance_heading'] ?? null,
            historicalSignificanceText: $raw['historical_significance_text'] ?? null,
            significanceImage: $raw['significance_image'] ?? null,
        );
    }
}
