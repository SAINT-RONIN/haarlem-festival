<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for venues section data.
 */
final readonly class VenuesData
{
    /**
     * @param VenueCardData[] $venues
     */
    public function __construct(
        public string $headingText,
        public array $venues,
        public int $currentPage,
        public int $totalPages,
        public int $totalVenues,
    ) {
    }
}
