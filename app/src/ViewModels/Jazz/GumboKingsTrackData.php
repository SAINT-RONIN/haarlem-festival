<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for a Gumbo Kings track card.
 */
final readonly class GumboKingsTrackData
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
