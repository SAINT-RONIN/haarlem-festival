<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionPrice;
use App\Models\EventSessionPriceFilter;

/**
 * Interface for EventSessionPrice repository operations.
 */
interface IEventSessionPriceRepository
{
    /**
     * Find prices using optional filters.
     *
     * @return EventSessionPrice[]
     */
    public function findPrices(EventSessionPriceFilter $filters = new EventSessionPriceFilter()): array;

    /**
     * Find prices grouped by session ID for a set of session IDs.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]>
     */
    public function findPricesBySessionIds(array $sessionIds): array;

    /**
     * Create or update a price for a session and tier.
     *
     * @param int $sessionId
     * @param int $priceTierId
     * @param float $price
     * @param string $currencyCode
     * @return bool Success status
     */
    public function upsert(int $sessionId, int $priceTierId, float $price, string $currencyCode = 'EUR'): bool;

    /**
     * Delete a price by session and tier.
     *
     * @param int $sessionId
     * @param int $priceTierId
     * @return bool Success status
     */
    public function deleteBySessionAndTier(int $sessionId, int $priceTierId): bool;
}
