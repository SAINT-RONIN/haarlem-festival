<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Typed carrier for artist create/update form fields.
 * Extracted from POST in CmsArtistsController, validated by CmsArtistsService.
 */
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
