<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single artist card on the jazz landing page — photo, name, style, and profile link.
 */
final readonly class ArtistCardData
{
    public function __construct(
        public string $name,
        public string $genre,
        public string $description,
        public string $imageUrl,
        public int $performanceCount,
        public string $firstPerformance,
        public string $morePerformancesText,
        public ?string $profileUrl = null,
    ) {}
}
