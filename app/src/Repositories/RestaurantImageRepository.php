<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RestaurantImage;
use App\Repositories\Interfaces\IRestaurantImageRepository;
use PDO;

/**
 * Repository for RestaurantImage database operations.
 */
class RestaurantImageRepository implements IRestaurantImageRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

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
        $stmt = $this->pdo->prepare('
            SELECT ri.*, ma.FilePath
            FROM RestaurantImage ri
            LEFT JOIN MediaAsset ma ON ri.MediaAssetId = ma.MediaAssetId
            WHERE ri.RestaurantId = :restaurantId
            ORDER BY ri.ImageType ASC, ri.SortOrder ASC
        ');
        $stmt->execute(['restaurantId' => $restaurantId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([RestaurantImage::class, 'fromRow'], $rows);
    }
}
