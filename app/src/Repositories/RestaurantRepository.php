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
     * Returns all active restaurants.
     *
     * @return Restaurant[]
     */
    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM Restaurant
            WHERE IsActive = 1
            ORDER BY Name ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Restaurant::class, 'fromRow'], $rows);
    }
}
