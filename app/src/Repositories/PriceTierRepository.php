<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\PriceTier;
use App\Repositories\Interfaces\IPriceTierRepository;
use PDO;

/**
 * Read-only access to the PriceTier lookup table (e.g. "Regular", "VIP").
 *
 * Price tiers are assigned to event session prices to distinguish ticket categories.
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
     *
     * @return PriceTier[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT PriceTierId, Name FROM PriceTier ORDER BY PriceTierId ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([PriceTier::class, 'fromRow'], $rows);
    }

    /**
     * Returns a price tier by ID.
     *
     * @param int $priceTierId
     * @return PriceTier|null
     */
    public function findById(int $priceTierId): ?PriceTier
    {
        $stmt = $this->pdo->prepare('SELECT PriceTierId, Name FROM PriceTier WHERE PriceTierId = :priceTierId');
        $stmt->execute(['priceTierId' => $priceTierId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? PriceTier::fromRow($result) : null;
    }
}
