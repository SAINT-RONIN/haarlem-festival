<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\MediaAsset;

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

    public static function fromModel(MediaAsset $asset): self
    {
        return new self(
            mediaAssetId: $asset->mediaAssetId,
            filePath: $asset->filePath,
            originalFileName: $asset->originalFileName,
            mimeType: $asset->mimeType,
            fileSize: self::formatFileSize($asset->fileSizeBytes),
            altText: $asset->altText,
            createdAt: $asset->createdAtUtc->format('d M Y, H:i'),
        );
    }

    private static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
