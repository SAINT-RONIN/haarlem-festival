<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for PriceTier repository.
 */
interface IPriceTierRepository
{
    /**
     * Returns all price tiers ordered by ID.
     *
     * @return array List of price tiers
     */
    public function findAll(): array;

    /**
     * Returns a price tier by ID.
     *
     * @param int $priceTierId The price tier ID
     * @return array|null Price tier data or null if not found
     */
    public function findById(int $priceTierId): ?array;
}

