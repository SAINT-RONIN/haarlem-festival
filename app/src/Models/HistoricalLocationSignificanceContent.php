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
}
