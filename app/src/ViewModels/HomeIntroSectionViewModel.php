<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeIntroSectionViewModel
{
    public function __construct(
        public string $title,
        public string $tagline,
        public string $descriptionHtml,
        public string $buttonLabel,
        public string $buttonUrl,
        public string $imageUrl,
        public string $imageAlt,
    ) {
    }
}
