<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionPrice;

/**
 * Interface for EventSessionPrice repository operations.
 */
interface IEventSessionPriceRepository
{
    /**
     * Find all prices for a session with tier names (joined query).
     *
     * @param int $sessionId
     * @return array<int, array{
     *     EventSessionPriceId: int,
     *     EventSessionId: int,
     *     PriceTierId: int,
     *     Price: string,
     *     CurrencyCode: string,
     *     VatRate: string,
     *     PriceTierName: string
     * }>
     */
    public function findBySessionId(int $sessionId): array;

    /**
     * Find all prices for multiple sessions.
     *
     * @param array<int> $sessionIds
     * @return array<int, EventSessionPrice[]> Prices keyed by session ID
     */
    public function findBySessionIds(array $sessionIds): array;

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

