<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * Data for a single supporting artist card on the Dance page.
 */
final readonly class SupportingArtistCardData
{
    public function __construct(
        public string $name,
        public string $genre,
        public string $imageUrl,
        public string $profileUrl,
    ) {}
}
