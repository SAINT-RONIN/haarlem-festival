<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single album on the jazz artist detail page — title, cover art, and Spotify link.
 */
final readonly class JazzArtistAlbumData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $year,
        public string $tag,
        public string $imageUrl,
    ) {}
}
