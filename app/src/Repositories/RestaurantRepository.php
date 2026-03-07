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
            ORDER BY r.Name ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Restaurant::class, 'fromRow'], $rows);
    }
}
