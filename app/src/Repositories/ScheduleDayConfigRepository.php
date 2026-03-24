<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ScheduleDayConfig;
use App\Models\ScheduleDayConfigFilter;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use PDO;

/**
 * Repository for ScheduleDayConfig database operations.
 */
class ScheduleDayConfigRepository implements IScheduleDayConfigRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return ScheduleDayConfig[]
     */
    public function findConfigs(ScheduleDayConfigFilter $filter = new ScheduleDayConfigFilter()): array
    {
        $includeEventTypeName = (bool)($filter->includeEventTypeName ?? false);

        $sql = $includeEventTypeName
            ? '
                SELECT
                    sdc.ScheduleDayConfigId,
                    sdc.EventTypeId,
                    sdc.DayOfWeek,
                    sdc.IsVisible,
                    et.Name AS EventTypeName
                FROM ScheduleDayConfig sdc
                LEFT JOIN EventType et ON sdc.EventTypeId = et.EventTypeId AND sdc.EventTypeId IS NOT NULL
                WHERE 1 = 1
            '
            : '
                SELECT
                    sdc.ScheduleDayConfigId,
                    sdc.EventTypeId,
                    sdc.DayOfWeek,
                    sdc.IsVisible
                FROM ScheduleDayConfig sdc
                WHERE 1 = 1
            ';

        $params = [];

        if ($filter->eventTypeId !== null) {
            $sql .= ' AND sdc.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$filter->eventTypeId;
        }

        $requestedOrder = is_string($filter->orderBy) ? $filter->orderBy : 'scope';
        $allowedOrders = [
            'scope' => ' ORDER BY (sdc.EventTypeId = 0) DESC, sdc.EventTypeId ASC, sdc.DayOfWeek ASC',
            'day' => ' ORDER BY sdc.DayOfWeek ASC',
        ];
        $sql .= $allowedOrders[$requestedOrder] ?? $allowedOrders['scope'];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([ScheduleDayConfig::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Upserts a visibility setting.
     */
    public function upsert(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO ScheduleDayConfig (EventTypeId, DayOfWeek, IsVisible)
            VALUES (:eventTypeId, :dayOfWeek, :isVisible)
            ON DUPLICATE KEY UPDATE IsVisible = :isVisible2
        ');
        $stmt->execute([
            'eventTypeId' => $eventTypeId ?? 0,
            'dayOfWeek' => $dayOfWeek,
            'isVisible' => $isVisible ? 1 : 0,
            'isVisible2' => $isVisible ? 1 : 0,
        ]);
    }
}
