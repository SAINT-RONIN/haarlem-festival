<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for single artist card data.
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
    ) {
    }
}
