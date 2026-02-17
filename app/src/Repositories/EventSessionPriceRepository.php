<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventSessionPrice;
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

    public function findPrices(array $filters = []): array
    {
        $sql = '
            SELECT EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate
            FROM EventSessionPrice
            WHERE 1 = 1
        ';
        $params = [];

        if (isset($filters['sessionId'])) {
            $sql .= ' AND EventSessionId = :sessionId';
            $params['sessionId'] = (int)$filters['sessionId'];
        }

        $sessionIds = $filters['sessionIds'] ?? null;
        if (is_array($sessionIds)) {
            $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
            if ($normalizedIds === []) {
                return [];
            }

            $inPlaceholders = [];
            foreach ($normalizedIds as $index => $sessionId) {
                $paramName = 'sessionId' . $index;
                $inPlaceholders[] = ':' . $paramName;
                $params[$paramName] = $sessionId;
            }

            $sql .= ' AND EventSessionId IN (' . implode(', ', $inPlaceholders) . ')';
        }

        $sql .= ' ORDER BY EventSessionId ASC, PriceTierId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $prices = array_map([EventSessionPrice::class, 'fromRow'], $rows);

        $groupBySession = (bool)($filters['groupBySession'] ?? false);
        if (!$groupBySession) {
            return $prices;
        }

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
