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
     * @param EventSessionPriceFilter|array<string, mixed> $filters
     * @return EventSessionPrice[]|array<int, EventSessionPrice[]>
     */
    public function findPrices(EventSessionPriceFilter|array $filters = new EventSessionPriceFilter()): array;

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
