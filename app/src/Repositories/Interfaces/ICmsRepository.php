<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CmsItem;
use App\DTOs\Filters\CmsItemFilter;
use App\Models\CmsPage;
use App\DTOs\Filters\CmsPageFilter;
use App\Models\CmsSection;
use App\DTOs\Filters\CmsSectionFilter;

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

    /**
     * Returns the CmsPage with the given slug, or null if not found.
     */
    public function findPageBySlug(string $slug): ?CmsPage;

    /**
     * Inserts a new CmsSection under the given page and returns the new section ID.
     */
    public function insertSection(int $cmsPageId, string $sectionKey): int;

    /**
     * Inserts or updates a TEXT CmsItem by section + key. Creates if not exists, updates if it does.
     */
    public function upsertCmsTextItem(int $cmsSectionId, string $itemKey, string $textValue): void;
}
