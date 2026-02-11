<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ICmsRepository
{
    public function getPageBySlug(string $slug): ?array;
    public function getPageById(int $cmsPageId): ?array;
    public function getSectionsByPageId(int $cmsPageId): array;
    public function getItemsBySectionId(int $cmsSectionId): array;
    public function getItemsBySectionKey(int $cmsPageId, string $sectionKey): array;
    public function findAllPages(): array;
    public function getItemById(int $cmsItemId): ?array;
    public function updateItem(int $cmsItemId, array $data): bool;
    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool;
}
