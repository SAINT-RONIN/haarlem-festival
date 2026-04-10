<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventSessionPrice;
use App\DTOs\Domain\Filters\EventSessionRelatedFilter;
use App\Repositories\Interfaces\IEventSessionPriceRepository;

// Per-session pricing by tier (standard, VIP, pay-what-you-like).
class EventSessionPriceRepository extends BaseRepository implements IEventSessionPriceRepository
{
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

    /** @return array<int, EventSessionPrice[]> keyed by EventSessionId */
    public function findPricesBySessionIds(array $sessionIds): array
    {
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

    // Composite key (EventSessionId, PriceTierId) determines insert vs update.
    // New rows default to 21% VAT.
    public function upsert(int $sessionId, int $priceTierId, float $price, string $currencyCode = 'EUR'): bool
    {
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
