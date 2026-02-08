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
}

