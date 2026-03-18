<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the Historical location introduction section containing the introduction text,
 * a fact about the location, and the location photo.
 */
final readonly class LocationIntroduction
{
    public function __construct(
        public string $headingText,
        public string $introText,
        public string $locationImagePath = '',
    ) {
    }
}
