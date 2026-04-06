<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the home page banner that invites visitors to explore the festival.
 */
final readonly class HomeExploreBannerViewModel
{
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $backgroundImageUrl,
    ) {
    }
}
