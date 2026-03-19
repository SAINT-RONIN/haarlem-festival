<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PageGalleryImage;

interface IPageGalleryImageRepository
{
    /**
     * Returns gallery images for a CMS page, optionally filtered by image type, ordered by SortOrder.
     *
     * @return PageGalleryImage[]
     */
    public function findByPageId(int $cmsPageId, ?string $imageType = null): array;
}
