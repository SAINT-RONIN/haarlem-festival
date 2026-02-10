<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for artists section data.
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

