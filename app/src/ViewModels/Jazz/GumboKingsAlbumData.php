<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for a Gumbo Kings featured album card.
 */
final readonly class GumboKingsAlbumData
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
