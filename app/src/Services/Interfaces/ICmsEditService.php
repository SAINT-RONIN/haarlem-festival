<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\CmsPage;
use App\Models\CmsPageEditData;
use App\Models\CmsUpdateResult;

/**
 * Interface for CMS page editing service.
 */
interface ICmsEditService
{
    /**
     * Gets a page with all its sections and items for editing.
     */
    public function getPageForEditing(int $pageId): ?CmsPageEditData;

    /**
     * Updates multiple CMS items from form submission.
     *
     * @param array<int|string, mixed> $items Array of item updates: [itemId => value_string]
     */
    public function updatePageItems(int $pageId, array $items): CmsUpdateResult;

    /**
     * Builds a route-aware preview URL for CMS page edit screens.
     *
     * @param array<int, array<string, mixed>> $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string;

    /**
     * Updates a single CMS item's media asset.
     */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool;
}
