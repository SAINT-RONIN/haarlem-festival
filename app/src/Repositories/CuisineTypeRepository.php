<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\CuisineType;
use App\Repositories\Interfaces\ICuisineTypeRepository;
use PDO;

/**
 * Read-only access to the CuisineType table via the RestaurantCuisine junction table.
 *
 * Cuisine types (e.g. "Italian", "Japanese") are linked to restaurants through a
 * many-to-many relationship. This repository resolves those associations.
 */
class CuisineTypeRepository implements ICuisineTypeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all cuisine types for a single restaurant, ordered by name.
     * Joins through the RestaurantCuisine junction table.
     *
     * @return CuisineType[]
     */
    public function findByRestaurantId(int $restaurantId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT ct.* FROM CuisineType ct
            INNER JOIN RestaurantCuisine rc ON ct.CuisineTypeId = rc.CuisineTypeId
            WHERE rc.RestaurantId = :restaurantId
            ORDER BY ct.Name ASC
        ');
        $stmt->execute(['restaurantId' => $restaurantId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([CuisineType::class, 'fromRow'], $rows);
    }

    /**
     * Batch-fetches cuisine types for multiple restaurants in one query to avoid N+1.
     * Results are grouped by RestaurantId for easy lookup.
     *
     * @param int[] $restaurantIds
     * @return array<int, CuisineType[]> Keyed by RestaurantId
     */
    public function findByRestaurantIds(array $restaurantIds): array
    {
        if ($restaurantIds === []) {
            return [];
        }

        // Build positional placeholders (?, ?, ...) for the IN clause
        $placeholders = implode(',', array_fill(0, count($restaurantIds), '?'));
        $stmt = $this->pdo->prepare("
            SELECT ct.*, rc.RestaurantId
            FROM CuisineType ct
            INNER JOIN RestaurantCuisine rc ON ct.CuisineTypeId = rc.CuisineTypeId
            WHERE rc.RestaurantId IN ({$placeholders})
            ORDER BY rc.RestaurantId ASC, ct.Name ASC
        ");
        $stmt->execute(array_values($restaurantIds));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $restaurantId = (int)$row['RestaurantId'];
            $grouped[$restaurantId][] = CuisineType::fromRow($row);
        }

        return $grouped;
    }
}
