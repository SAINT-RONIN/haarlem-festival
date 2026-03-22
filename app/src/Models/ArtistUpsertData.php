<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ArtistUpsertData
{
    public function __construct(
        public string $name,
        public string $style,
        public string $bioHtml,
        public ?int   $imageAssetId,
        public bool   $isActive,
    ) {}
}
