<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the PageGalleryImage table.
 *
 * Gallery images associated with CMS pages rather than specific events.
 */
final readonly class PageGalleryImage
{
    public function __construct(
        public int    $pageGalleryImageId,
        public int    $cmsPageId,
        public string $imagePath,
        public string $imageType,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            pageGalleryImageId: (int)($row['PageGalleryImageId'] ?? throw new \InvalidArgumentException('Missing required field: PageGalleryImageId')),
            cmsPageId:          (int)($row['CmsPageId'] ?? throw new \InvalidArgumentException('Missing required field: CmsPageId')),
            imagePath:          (string)($row['ImagePath'] ?? throw new \InvalidArgumentException('Missing required field: ImagePath')),
            imageType:          (string)($row['ImageType'] ?? throw new \InvalidArgumentException('Missing required field: ImageType')),
            sortOrder:          (int)($row['SortOrder'] ?? throw new \InvalidArgumentException('Missing required field: SortOrder')),
        );
    }
}
