<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsArtistListItemViewModel
{
    public function __construct(
        public int    $artistId,
        public string $name,
        public string $style,
        public bool   $isActive,
        public string $createdAt,
    ) {}
}
