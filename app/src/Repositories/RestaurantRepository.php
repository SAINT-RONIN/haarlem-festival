<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
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
     * Returns all active restaurants.
     *
     * @return array Array of Restaurant rows
     */
    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT RestaurantId, Name, AddressLine, City, Stars, CuisineType
            FROM Restaurant
            WHERE IsActive = 1
            ORDER BY Name ASC
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
