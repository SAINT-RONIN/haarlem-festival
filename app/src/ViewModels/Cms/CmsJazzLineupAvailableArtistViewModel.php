<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsJazzLineupAvailableArtistViewModel
{
    public function __construct(
        public int $artistId,
        public string $name,
        public string $style,
        public string $description,
        public string $imageUrl,
        public int $sortOrder,
        public string $addAction,
    ) {}
}
