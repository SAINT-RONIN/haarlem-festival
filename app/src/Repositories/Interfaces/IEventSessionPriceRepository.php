<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionPrice;
use App\DTOs\Domain\Filters\EventSessionRelatedFilter;

/**
 * Contract for managing per-session pricing by tier (e.g. standard, VIP, pay-what-you-like).
 * Each price row links a session to a price tier with an amount, currency, and VAT rate.
 * Supports bulk lookup keyed by session ID for efficiently hydrating session listings.
 */
interface IEventSessionPriceRepository
{
    /**
     * Retrieves prices with optional filtering by session ID.
     *
     * @return EventSessionPrice[]
     */
    public function findPrices(EventSessionRelatedFilter $filters = new EventSessionRelatedFilter()): array;

    /**
     * Batch-fetches prices for multiple sessions in one query, grouped by session ID.
     * Used to efficiently attach price data when rendering session lists.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]> Keyed by EventSessionId.
     */
    public function findPricesBySessionIds(array $sessionIds): array;

    /**
     * Creates or updates a price entry for a session+tier combination. New rows
     * default to 21% VAT. Uses the composite key (EventSessionId, PriceTierId)
     * to determine whether to insert or update.
     */
    public function upsert(int $sessionId, int $priceTierId, float $price, string $currencyCode = 'EUR'): bool;

    /**
     * Removes a specific price tier from a session (e.g. when an admin removes VIP pricing).
     */
    public function deleteBySessionAndTier(int $sessionId, int $priceTierId): bool;
}
