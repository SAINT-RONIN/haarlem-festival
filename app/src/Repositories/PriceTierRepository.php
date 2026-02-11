<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IPriceTierRepository;
use PDO;

/**
 * Repository for PriceTier database operations.
 */
class PriceTierRepository implements IPriceTierRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all price tiers ordered by ID.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT PriceTierId, Name FROM PriceTier ORDER BY PriceTierId ASC');
        return $stmt->fetchAll();
    }

    /**
     * Returns a price tier by ID.
     */
    public function findById(int $priceTierId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT PriceTierId, Name FROM PriceTier WHERE PriceTierId = :priceTierId');
        $stmt->execute(['priceTierId' => $priceTierId]);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }
}

