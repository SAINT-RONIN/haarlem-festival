<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for a Jazz artist track card.
 */
final readonly class JazzArtistTrackData
{
    public function __construct(
        public string $title,
        public string $album,
        public string $description,
        public string $duration,
        public string $imageUrl,
        public string $progressClass,
    ) {
    }
}
