<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for a Jazz artist featured album card.
 */
final readonly class JazzArtistAlbumData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $year,
        public string $tag,
        public string $imageUrl,
    ) {
    }
}
