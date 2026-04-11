<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * Section data for the Dance page supporting artists grid.
 */
final readonly class SupportingArtistsData
{
    /**
     * @param SupportingArtistCardData[] $artists
     */
    public function __construct(
        public string $headingText,
        public array $artists,
    ) {}
}
