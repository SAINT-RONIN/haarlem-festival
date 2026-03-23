<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\MediaAsset;

/**
 * Interface for MediaAsset repository operations.
 */
interface IMediaAssetRepository
{
    /**
     * Finds a media asset by ID.
     *
     * @param int $mediaAssetId
     * @return MediaAsset|null
     */
    public function findById(int $mediaAssetId): ?MediaAsset;

    /**
     * @param int[] $ids
     * @return array<int, MediaAsset> Keyed by MediaAssetId
     */
    public function findByIds(array $ids): array;

    /**
     * Inserts a new media asset record and returns the generated ID.
     */
    public function create(array $data): int;

    /**
     * Updates a media asset's columns and returns whether any row was affected.
     */
    public function update(int $mediaAssetId, array $data): bool;

    /**
     * Deletes a media asset by its ID.
     */
    public function delete(int $mediaAssetId): bool;

    /**
     * Links a media asset to a CMS item.
     *
     * @param int $mediaAssetId Media asset ID
     * @param int $cmsItemId CMS item ID
     * @return bool Success status
     */
    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool;

    /**
     * Returns all media assets ordered by newest first.
     *
     * @return MediaAsset[]
     */
    public function findAll(): array;
}
