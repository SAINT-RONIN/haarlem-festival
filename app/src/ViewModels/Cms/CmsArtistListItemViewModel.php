<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single artist row in the CMS artists list table.
 */
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
