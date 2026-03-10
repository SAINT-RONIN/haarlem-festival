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
     * Returns a single restaurant by ID with all detail page images, or null if not found.
     *
     * Joins MediaAsset multiple times to resolve image paths for:
     * card image, gallery (3), about, chef, menu (2), reservation.
     */
    public function findById(int $id): ?Restaurant
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*,
                   ma.FilePath     AS ImagePath,
                   g1.FilePath     AS GalleryImage1Path,
                   g2.FilePath     AS GalleryImage2Path,
                   g3.FilePath     AS GalleryImage3Path,
                   ab.FilePath     AS AboutImagePath,
                   ch.FilePath     AS ChefImagePath,
                   m1.FilePath     AS MenuImage1Path,
                   m2.FilePath     AS MenuImage2Path,
                   rv.FilePath     AS ReservationImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId            = ma.MediaAssetId
            LEFT JOIN MediaAsset g1 ON r.GalleryImage1AssetId    = g1.MediaAssetId
            LEFT JOIN MediaAsset g2 ON r.GalleryImage2AssetId    = g2.MediaAssetId
            LEFT JOIN MediaAsset g3 ON r.GalleryImage3AssetId    = g3.MediaAssetId
            LEFT JOIN MediaAsset ab ON r.AboutImageAssetId       = ab.MediaAssetId
            LEFT JOIN MediaAsset ch ON r.ChefImageAssetId        = ch.MediaAssetId
            LEFT JOIN MediaAsset m1 ON r.MenuImage1AssetId       = m1.MediaAssetId
            LEFT JOIN MediaAsset m2 ON r.MenuImage2AssetId       = m2.MediaAssetId
            LEFT JOIN MediaAsset rv ON r.ReservationImageAssetId = rv.MediaAssetId
            WHERE r.RestaurantId = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? Restaurant::fromRow($row) : null;
    }
}
