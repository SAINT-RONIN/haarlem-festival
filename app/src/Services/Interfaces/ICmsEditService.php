<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\CmsPage;
use App\Models\CmsPageEditData;
use App\Models\CmsSectionEditData;
use App\Models\CmsUpdateResult;

/**
 * Contract for CMS page editing: loading page/section/item trees for the editor,
 * persisting content updates with per-type validation, and resolving preview URLs.
 */
interface ICmsEditService
{
    /**
     * Loads a page with all its sections and editable items (including media asset metadata).
     * Returns null when the page ID does not exist.
     */
    public function getPageForEditing(int $pageId): ?CmsPageEditData;

    /**
     * Validates and persists updates for multiple CMS items in a single form submission.
     *
     * @param array<int|string, mixed> $items [itemId => value_string]
     * @throws \App\Exceptions\CmsEditException if an item ID does not belong to the given page
     */
    public function updatePageItems(int $pageId, array $items): CmsUpdateResult;

    /**
     * Builds a route-aware preview URL for CMS page edit screens.
     *
     * @param CmsSectionEditData[] $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string;

    /**
     * Updates a single CMS item's media asset.
     */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool;
}
