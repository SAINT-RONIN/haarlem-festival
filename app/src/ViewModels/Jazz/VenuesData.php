<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for venues section data.
 */
final readonly class VenuesData
{
    /**
     * @param VenueData[] $venues
     */
    public function __construct(
        public string $headingText,
        public string $subheadingText,
        public string $descriptionText,
        public array $venues,
    ) {
    }
}

