<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventSessionPrice;
use App\DTOs\Filters\EventSessionRelatedFilter;
use App\Repositories\Interfaces\IEventSessionPriceRepository;

/**
 * Manages the EventSessionPrice table, which stores per-session pricing by tier
 * (e.g. standard, VIP, pay-what-you-like). Each row links a session to a price tier
 * with an amount, currency, and VAT rate. Supports bulk lookup keyed by session ID
 * for efficiently hydrating session listings with their price options.
 */
class EventSessionPriceRepository extends BaseRepository implements IEventSessionPriceRepository
{
    /**
     * Retrieves prices with optional filtering by session ID. Uses a 'WHERE 1=1' base
     * to simplify dynamic condition appending.
     *
     * @return EventSessionPrice[] Ordered by session then tier. Empty array if no matches.
     */
    public function findPrices(EventSessionRelatedFilter $filters = new EventSessionRelatedFilter()): array
    {
        $sql = '
            SELECT EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate
            FROM EventSessionPrice
            WHERE 1 = 1
        ';
        $params = [];

        if ($filters->sessionId !== null) {
            $sql .= ' AND EventSessionId = :sessionId';
            $params['sessionId'] = $filters->sessionId;
        }

        $sql .= ' ORDER BY EventSessionId ASC, PriceTierId ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => EventSessionPrice::fromRow($row));
    }

    /**
     * Batch-fetches prices for multiple sessions in a single query, then groups them
     * by session ID. Used to efficiently attach price data when rendering session lists.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]> Keyed by EventSessionId. Missing IDs are absent (not empty arrays).
     */
    public function findPricesBySessionIds(array $sessionIds): array
    {
        // Deduplicate and cast IDs to int before building the IN clause
        $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
        if ($normalizedIds === []) {
            return [];
        }

        $inClause = $this->buildInClause($normalizedIds, 'sessionId');

        $sql = '
            SELECT EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate
            FROM EventSessionPrice
            WHERE EventSessionId IN (' . $inClause['placeholders'] . ')
            ORDER BY EventSessionId ASC, PriceTierId ASC
        ';

        $prices = $this->fetchAll($sql, $inClause['params'], fn(array $row) => EventSessionPrice::fromRow($row));

        return $this->groupByKey($prices, 'eventSessionId');
    }

    /**
     * Creates or updates a price entry for a session+tier combination. New rows
     * default to 21% VAT. The composite key (EventSessionId, PriceTierId) determines
     * whether to insert or update.
     *
     * @inheritDoc
     */
    public function upsert(int $sessionId, int $priceTierId, float $price, string $currencyCode = 'EUR'): bool
    {
        // Check if a price row already exists for this session+tier pair
        $stmt = $this->execute(
            'SELECT EventSessionPriceId FROM EventSessionPrice
            WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId',
            ['sessionId' => $sessionId, 'priceTierId' => $priceTierId],
        );
        $existing = $stmt->fetch();

        $params = [
            'sessionId' => $sessionId,
            'priceTierId' => $priceTierId,
            'price' => $price,
            'currencyCode' => $currencyCode,
        ];

        if ($existing) {
            $this->execute(
                'UPDATE EventSessionPrice
                SET Price = :price, CurrencyCode = :currencyCode
                WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId',
                $params,
            );
        } else {
            $this->execute(
                'INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
                VALUES (:sessionId, :priceTierId, :price, :currencyCode, 21.00)',
                $params,
            );
        }

        return true;
    }

    /**
     * Removes a specific price tier from a session (e.g. when an admin removes VIP pricing).
     *
     * @inheritDoc
     */
    public function deleteBySessionAndTier(int $sessionId, int $priceTierId): bool
    {
        $this->execute(
            'DELETE FROM EventSessionPrice
            WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId',
            ['sessionId' => $sessionId, 'priceTierId' => $priceTierId],
        );

        return true;
    }
}
