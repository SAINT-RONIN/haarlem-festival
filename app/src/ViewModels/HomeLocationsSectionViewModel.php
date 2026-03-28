<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeLocationsSectionViewModel
{
    public function __construct(
        public string $title,
        public string $filterLabel,
        public string $filterTitle,
        public string $allLabel,
        public string $jazzLabel,
        public string $danceLabel,
        public string $historyLabel,
        public string $restaurantsLabel,
        public string $storiesLabel,
    ) {
    }
}
