<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CmsItem;
use App\Models\CmsPage;
use App\Models\CmsSection;

interface ICmsRepository
{
    /**
     * @param array{cmsPageId?: int, slug?: string, includeLastUpdated?: bool} $filters
     * @return CmsPage[]
     */
    public function findPages(array $filters = []): array;

    /**
     * @param array{cmsPageId?: int, sectionKey?: string} $filters
     * @return CmsSection[]
     */
    public function findSections(array $filters = []): array;

    /**
     * @param array{cmsSectionId?: int, cmsPageId?: int, sectionKey?: string, cmsItemId?: int} $filters
     * @return CmsItem[]
     */
    public function findItems(array $filters = []): array;

    public function updateItem(int $cmsItemId, array $data): bool;

    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool;
}
