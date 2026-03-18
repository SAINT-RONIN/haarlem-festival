<?php

declare(strict_types=1);

namespace App\ViewModels\History;

final readonly class LocationHeroData
{
    public function __construct(
        public string $mainTitle,
        public string $subtitle,
        public string $buttonText,
        public string $buttonLink,
        public string $mapImageUrl,
        public string $backgroundImageUrl,
        public string $currentPage,
    ) {
    }
}
