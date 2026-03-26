<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventSessionPrice;
use App\Models\EventSessionPriceFilter;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use PDO;

/**
 * Repository for EventSessionPrice database operations.
 */
class EventSessionPriceRepository implements IEventSessionPriceRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return EventSessionPrice[]
     */
    public function findPrices(EventSessionPriceFilter $filters = new EventSessionPriceFilter()): array
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventSessionPrice::class, 'fromRow'], $rows);
    }

    /**
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]>
     */
    public function findPricesBySessionIds(array $sessionIds): array
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
        if ($normalizedIds === []) {
            return [];
        }

        $params = [];
        $inPlaceholders = [];
        foreach ($normalizedIds as $index => $sessionId) {
            $paramName = 'sessionId' . $index;
            $inPlaceholders[] = ':' . $paramName;
            $params[$paramName] = $sessionId;
        }

        $sql = '
            SELECT EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate
            FROM EventSessionPrice
            WHERE EventSessionId IN (' . implode(', ', $inPlaceholders) . ')
            ORDER BY EventSessionId ASC, PriceTierId ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $prices = array_map([EventSessionPrice::class, 'fromRow'], $rows);

        $grouped = [];
        foreach ($prices as $price) {
            $grouped[$price->eventSessionId][] = $price;
        }

        return $grouped;
    }

    /**
     * @inheritDoc
     */
    public function upsert(int $sessionId, int $priceTierId, float $price, string $currencyCode = 'EUR'): bool
    {
        // Check if exists
        $stmt = $this->pdo->prepare('
            SELECT EventSessionPriceId FROM EventSessionPrice
            WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId
        ');
        $stmt->execute(['sessionId' => $sessionId, 'priceTierId' => $priceTierId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update
            $stmt = $this->pdo->prepare('
                UPDATE EventSessionPrice
                SET Price = :price, CurrencyCode = :currencyCode
                WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId
            ');
        } else {
            // Insert
            $stmt = $this->pdo->prepare('
                INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
                VALUES (:sessionId, :priceTierId, :price, :currencyCode, 21.00)
            ');
        }

        return $stmt->execute([
            'sessionId' => $sessionId,
            'priceTierId' => $priceTierId,
            'price' => $price,
            'currencyCode' => $currencyCode,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function deleteBySessionAndTier(int $sessionId, int $priceTierId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM EventSessionPrice
            WHERE EventSessionId = :sessionId AND PriceTierId = :priceTierId
        ');

        return $stmt->execute([
            'sessionId' => $sessionId,
            'priceTierId' => $priceTierId,
        ]);
    }
}
