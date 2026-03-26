<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

final readonly class JazzArtistHeroData
{
    public function __construct(
        public string $heroTitle,
        public string $heroSubtitle,
        public string $heroBackgroundImageUrl,
        public string $originText,
        public string $formedText,
        public string $performancesText,
        public string $heroBackButtonText,
        public string $heroBackButtonUrl,
        public string $heroReserveButtonText,
    ) {}
}
