<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `PageGalleryImage` table.
 */
class PageGalleryImage
{
    public function __construct(
        public readonly int    $pageGalleryImageId,
        public readonly int    $cmsPageId,
        public readonly string $imagePath,
        public readonly string $imageType,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            pageGalleryImageId: (int)$row['PageGalleryImageId'],
            cmsPageId:          (int)$row['CmsPageId'],
            imagePath:          (string)$row['ImagePath'],
            imageType:          (string)$row['ImageType'],
            sortOrder:          (int)$row['SortOrder'],
        );
    }
}
