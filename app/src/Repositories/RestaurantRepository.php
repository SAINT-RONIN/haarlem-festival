<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Restaurant;
use App\Repositories\Interfaces\IRestaurantRepository;

/**
 * Repository for Restaurant database operations.
 */
class RestaurantRepository extends BaseRepository implements IRestaurantRepository
{
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
        return $this->fetchAll(
            'SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.IsActive = 1
            ORDER BY r.RestaurantId ASC',
            [],
            fn(array $row) => Restaurant::fromRow($row),
        );
    }

    /**
     * Returns a single restaurant by ID with its card image path, or null if not found.
     *
     * Detail section images are now stored in RestaurantImage and fetched separately.
     */
    public function findById(int $id): ?Restaurant
    {
        return $this->fetchOne(
            'SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.RestaurantId = :id
            LIMIT 1',
            [':id' => $id],
            fn(array $row) => Restaurant::fromRow($row),
        );
    }

    /**
     * Returns all restaurants (including inactive), optionally filtered by name search.
     * Orders by Name ASC.
     *
     * @return Restaurant[]
     */
    public function findAll(?string $search = null): array
    {
        $sql = 'SELECT r.*, ma.FilePath AS ImagePath FROM Restaurant r
                LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId';
        $params = [];
        if ($search !== null && $search !== '') {
            $sql .= ' WHERE r.Name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY r.Name ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => Restaurant::fromRow($row));
    }

    /** Soft-deletes a restaurant by marking it inactive instead of removing the row. */
    public function delete(int $id): void
    {
        $this->execute('UPDATE Restaurant SET IsActive = 0 WHERE RestaurantId = :id', [':id' => $id]);
    }
}
