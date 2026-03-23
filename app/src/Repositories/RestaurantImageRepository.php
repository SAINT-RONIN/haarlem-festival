<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\RestaurantImage;
use PDO;

/**
 * Repository for RestaurantImage database operations.
 */
class RestaurantImageRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all images for a restaurant ordered by ImageType, then SortOrder.
     *
     * @return RestaurantImage[]
     */
    public function findByRestaurantId(int $restaurantId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM RestaurantImage
            WHERE RestaurantId = :restaurantId
            ORDER BY ImageType ASC, SortOrder ASC
        ');
        $stmt->execute(['restaurantId' => $restaurantId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([RestaurantImage::class, 'fromRow'], $rows);
    }
}