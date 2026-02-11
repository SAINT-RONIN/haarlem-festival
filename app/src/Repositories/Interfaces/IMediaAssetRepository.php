<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for MediaAsset repository operations.
 */
interface IMediaAssetRepository
{
    public function findById(int $mediaAssetId): ?array;

    public function create(array $data): int;

    public function update(int $mediaAssetId, array $data): bool;

    public function delete(int $mediaAssetId): bool;

    /**
     * Links a media asset to a CMS item.
     *
     * @param int $mediaAssetId Media asset ID
     * @param int $cmsItemId CMS item ID
     * @return bool Success status
     */
    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool;
}

