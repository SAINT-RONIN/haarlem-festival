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

    /**
     * Find all prices for a session with tier names (joined query).
     *
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
    public function findBySessionId(int $sessionId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT esp.EventSessionPriceId, esp.EventSessionId, esp.PriceTierId,
                   esp.Price, esp.CurrencyCode, esp.VatRate,
                   pt.Name AS PriceTierName
            FROM EventSessionPrice esp
            INNER JOIN PriceTier pt ON esp.PriceTierId = pt.PriceTierId
            WHERE esp.EventSessionId = :sessionId
            ORDER BY esp.PriceTierId ASC
        ');
        $stmt->execute(['sessionId' => $sessionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find all prices for multiple sessions.
     *
     * @param array<int> $sessionIds
     * @return array<int, EventSessionPrice[]>
     */
    public function findBySessionIds(array $sessionIds): array
    {
        if (empty($sessionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($sessionIds), '?'));
        $stmt = $this->pdo->prepare("
            SELECT EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate
            FROM EventSessionPrice
            WHERE EventSessionId IN ($placeholders)
            ORDER BY EventSessionId, PriceTierId ASC
        ");
        $stmt->execute($sessionIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by session ID
        $grouped = [];
        foreach ($rows as $row) {
            $sid = (int)$row['EventSessionId'];
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [];
            }
            $grouped[$sid][] = EventSessionPrice::fromRow($row);
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
