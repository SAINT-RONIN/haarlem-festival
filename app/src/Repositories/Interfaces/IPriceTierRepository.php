<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PriceTier;

/**
 * Interface for PriceTier repository.
 */
interface IPriceTierRepository
{
    /**
     * Returns all price tiers ordered by ID.
     *
     * @return PriceTier[]
     */
    public function findAll(): array;

    /**
     * Returns a price tier by ID.
     *
     * @param int $priceTierId The price tier ID
     * @return PriceTier|null
     */
    public function findById(int $priceTierId): ?PriceTier;
}
