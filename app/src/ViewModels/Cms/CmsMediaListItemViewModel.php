<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single media asset card in the CMS media library.
 */
final readonly class CmsMediaListItemViewModel
{
    public function __construct(
        public int $mediaAssetId,
        public string $filePath,
        public string $originalFileName,
        public string $mimeType,
        public string $fileSize,
        public string $altText,
        public string $createdAt,
    ) {}
}
