<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RestaurantImage;
use App\Repositories\Interfaces\IRestaurantImageRepository;

/**
 * Repository for RestaurantImage database operations.
 */
class RestaurantImageRepository extends BaseRepository implements IRestaurantImageRepository
{
    /**
     * Returns all images for a restaurant with their file path from MediaAsset.
     *
     * Uses LEFT JOIN so images whose MediaAsset row was deleted are still returned (filePath = null).
     * Ordered by ImageType first, then SortOrder, so callers can group by type predictably.
     *
     * @return RestaurantImage[]
     */
    public function findByRestaurantId(int $restaurantId): array
    {
        $sql = '
            SELECT ri.*, ma.FilePath
            FROM RestaurantImage ri
            LEFT JOIN MediaAsset ma ON ri.MediaAssetId = ma.MediaAssetId
            WHERE ri.RestaurantId = :restaurantId
            ORDER BY ri.ImageType ASC, ri.SortOrder ASC
        ';

        return $this->fetchAll($sql, ['restaurantId' => $restaurantId], fn(array $row) => RestaurantImage::fromRow($row));
    }
}
