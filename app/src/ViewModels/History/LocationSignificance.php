<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the historical location's architectural and historical significance section.
 * Containing two texts about architectural and historical significance and the location's photo.
 */
final readonly class LocationSignificance
{
    public function __construct(
        public string $architecturalSignificanceHeadingText,
        public string $architecturalSignificanceText,
        public string $historicalSignificanceHeadingText,
        public string $historicalSignificanceText,
        public string $locationImagePath = '',
    ) {}
}
