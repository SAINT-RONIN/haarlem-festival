<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeExploreBannerViewModel
{
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $backgroundImageUrl,
    ) {
    }
}
