<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single track on the jazz artist detail page — title, duration, and audio preview URL.
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
    ) {}
}
