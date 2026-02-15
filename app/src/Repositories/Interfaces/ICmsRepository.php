<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CmsItem;
use App\Models\CmsPage;
use App\Models\CmsSection;

interface ICmsRepository
{
    public function getPageBySlug(string $slug): ?CmsPage;

    public function getPageById(int $cmsPageId): ?CmsPage;

    /**
     * @return CmsSection[]
     */
    public function getSectionsByPageId(int $cmsPageId): array;

    /**
     * @return CmsItem[]
     */
    public function getItemsBySectionId(int $cmsSectionId): array;

    /**
     * @return CmsItem[]
     */
    public function getItemsBySectionKey(int $cmsPageId, string $sectionKey): array;

    /**
     * @return array<int, array{CmsPageId: int, Title: string, Slug: string, UpdatedAtUtc: ?string}>
     */
    public function findAllPages(): array;

    public function getItemById(int $cmsItemId): ?CmsItem;

    public function updateItem(int $cmsItemId, array $data): bool;

    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool;
}
