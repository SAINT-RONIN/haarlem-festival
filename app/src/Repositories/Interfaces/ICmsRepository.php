<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CmsItem;
use App\Models\CmsItemFilter;
use App\Models\CmsPage;
use App\Models\CmsPageFilter;
use App\Models\CmsSection;
use App\Models\CmsSectionFilter;

interface ICmsRepository
{
    /**
     * @return CmsPage[]
     */
    public function findPages(CmsPageFilter $filter): array;

    /**
     * @return CmsSection[]
     */
    public function findSections(CmsSectionFilter $filter): array;

    /**
     * @return CmsItem[]
     */
    public function findItems(CmsItemFilter $filter): array;

    /**
     * @param array<string, mixed> $data
     */
    public function updateItem(int $cmsItemId, array $data): bool;

    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool;
}
