<?php

declare(strict_types=1);

namespace App\ViewModels\History;

final readonly class LocationHero
{
    public function __construct(
        public string $mainTitle,
        public string $subtitle,
        public string $buttonText,
        public string $buttonLink,
        public string $backgroundImageUrl,
        public string $currentPage,
        public string $mapImageUrl,
    ) {}
}
