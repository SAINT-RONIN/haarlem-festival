<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PriceTier;

/**
 * Contract for reading price tier lookup data (e.g. "Regular", "VIP").
 * Price tiers categorize ticket prices within event sessions.
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
