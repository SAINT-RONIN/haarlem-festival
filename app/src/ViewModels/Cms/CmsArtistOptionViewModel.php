<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * Lightweight artist representation used in dropdown selectors.
 */
final readonly class CmsArtistOptionViewModel
{
    public function __construct(
        public int $artistId,
        public string $name,
        public string $style,
        public string $description,
        public ?int $imageAssetId,
        public string $imageUrl,
    ) {}
}
