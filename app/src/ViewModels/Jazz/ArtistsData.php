<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Section data for the jazz page artists carousel — section title and array of artist cards.
 */
final readonly class ArtistsData
{
    /**
     * @param ArtistCardData[] $artists
     */
    public function __construct(
        public string $headingText,
        public array $artists,
        public int $currentPage,
        public int $totalPages,
        public int $totalArtists,
    ) {
    }
}
