<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * Container for the "Read more about these locations" section.
 */
final readonly class VenuesData
{
    /**
     * @param VenueCardData[] $venues
     */
    public function __construct(
        public string $headingText,
        public string $viewMoreLabel,
        public array $venues,
    ) {
    }
}
