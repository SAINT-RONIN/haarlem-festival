<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CmsItem;
use App\Models\CmsItemFilter;
use App\Models\CmsPage;
use App\Models\CmsPageFilter;
use App\Models\CmsSection;
use App\Models\CmsSectionFilter;

/**
 * Defines persistence operations for CMS pages, sections, and items.
 */
interface ICmsRepository
{
    /**
     * Queries CMS pages matching the given filter criteria.
     *
     * @return CmsPage[]
     */
    public function findPages(CmsPageFilter $filter): array;

    /**
     * Queries CMS sections matching the given filter criteria.
     *
     * @return CmsSection[]
     */
    public function findSections(CmsSectionFilter $filter): array;

    /**
     * Queries CMS items matching the given filter criteria.
     *
     * @return CmsItem[]
     */
    public function findItems(CmsItemFilter $filter): array;

    /**
     * Updates a CMS item's columns (e.g. value string) and returns whether any row was affected.
     *
     * @param array<string, mixed> $data
     */
    public function updateItem(int $cmsItemId, array $data): bool;

    /**
     * Sets or clears the media asset linked to a CMS item.
     */
    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool;
}
