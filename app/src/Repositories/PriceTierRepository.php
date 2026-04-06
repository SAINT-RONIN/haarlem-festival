<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PriceTier;
use App\Repositories\Interfaces\IPriceTierRepository;

/**
 * Read-only access to the PriceTier lookup table (e.g. "Regular", "VIP").
 *
 * Price tiers are assigned to event session prices to distinguish ticket categories.
 */
class PriceTierRepository extends BaseRepository implements IPriceTierRepository
{
    /**
     * Returns all price tiers ordered by ID.
     *
     * @return PriceTier[]
     */
    public function findAll(): array
    {
        return $this->fetchAll(
            'SELECT PriceTierId, Name FROM PriceTier ORDER BY PriceTierId ASC',
            [],
            fn(array $row) => PriceTier::fromRow($row),
        );
    }

    /**
     * Returns a price tier by ID.
     *
     * @param int $priceTierId
     * @return PriceTier|null
     */
    public function findById(int $priceTierId): ?PriceTier
    {
        return $this->fetchOne(
            'SELECT PriceTierId, Name FROM PriceTier WHERE PriceTierId = :priceTierId',
            ['priceTierId' => $priceTierId],
            fn(array $row) => PriceTier::fromRow($row),
        );
    }
}
