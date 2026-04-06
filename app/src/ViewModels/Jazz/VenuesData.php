<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Section data for the jazz page venues section — section heading and array of venue blocks.
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
