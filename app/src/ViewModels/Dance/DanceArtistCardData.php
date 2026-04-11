<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * Data for a single artist card on the Dance page.
 * Used for both headliners and supporting artists — same structure, different layout context.
 */
final readonly class DanceArtistCardData
{
    public function __construct(
        public string $name,
        public string $genre,
        public string $imageUrl,
        public string $profileUrl,
    ) {}
}
