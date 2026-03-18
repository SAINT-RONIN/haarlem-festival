<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Restaurant;
use App\Repositories\Interfaces\IRestaurantRepository;
use PDO;

/**
 * Repository for Restaurant database operations.
 */
class RestaurantRepository implements IRestaurantRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all active restaurants with their image path from MediaAsset.
     *
     * Uses LEFT JOIN so restaurants without an image are still included.
     * The ImagePath column comes from MediaAsset.FilePath.
     *
     * @return Restaurant[]
     */
    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.IsActive = 1
            ORDER BY r.RestaurantId ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Restaurant::class, 'fromRow'], $rows);
    }

    /**
     * Returns a single restaurant by ID with its card image path, or null if not found.
     *
     * Detail section images are now stored in RestaurantImage and fetched separately.
     */
    public function findById(int $id): ?Restaurant
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.RestaurantId = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? Restaurant::fromRow($row) : null;
    }
}
