<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ScheduleDayConfig;
use App\DTOs\Filters\ScheduleDayConfigFilter;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use PDO;

/**
 * Manages the ScheduleDayConfig table, which controls which days of the week
 * are visible on the public schedule for each event type.
 *
 * Rows can be global (EventTypeId = 0) or scoped to a specific event type.
 */
class ScheduleDayConfigRepository implements IScheduleDayConfigRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retrieves schedule-day visibility configs, optionally including event type names.
     *
     * Supports two sort orders:
     * - "scope": global configs first (EventTypeId = 0), then by event type and day
     * - "day": purely by day of week
     *
     * @return ScheduleDayConfig[]
     */
    public function findConfigs(ScheduleDayConfigFilter $filter = new ScheduleDayConfigFilter()): array
    {
        $includeEventTypeName = (bool)($filter->includeEventTypeName ?? false);

        // When event type name is needed, join EventType to resolve the display label
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
     * Inserts or updates a day-visibility setting using MySQL ON DUPLICATE KEY UPDATE.
     * The unique key is (EventTypeId, DayOfWeek), so repeated calls for the same
     * combination simply flip the IsVisible flag.
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
