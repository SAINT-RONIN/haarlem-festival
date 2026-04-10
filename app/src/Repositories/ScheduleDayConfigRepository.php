<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ScheduleDayConfig;
use App\DTOs\Domain\Filters\ScheduleDayConfigFilter;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;

// Controls which days of the week are visible on the public schedule.
// Rows can be global (EventTypeId = 0) or scoped to a specific event type.
class ScheduleDayConfigRepository extends BaseRepository implements IScheduleDayConfigRepository
{
    public function findConfigs(ScheduleDayConfigFilter $filter = new ScheduleDayConfigFilter()): array
    {
        $params = [];
        $sql = $this->buildConfigQuery((bool) ($filter->includeEventTypeName ?? false));
        $sql .= $this->buildEventTypeFilterClause($filter->eventTypeId, $params);
        $sql .= $this->resolveOrderClause($filter->orderBy);

        return $this->fetchAll($sql, $params, fn(array $row) => ScheduleDayConfig::fromRow($row));
    }

    private function buildConfigQuery(bool $includeEventTypeName): string
    {
        if ($includeEventTypeName) {
            return '
                SELECT
                    sdc.ScheduleDayConfigId,
                    sdc.EventTypeId,
                    sdc.DayOfWeek,
                    sdc.IsVisible,
                    et.Name AS EventTypeName
                FROM ScheduleDayConfig sdc
                LEFT JOIN EventType et ON sdc.EventTypeId = et.EventTypeId AND sdc.EventTypeId IS NOT NULL
                WHERE 1 = 1
            ';
        }

        return '
            SELECT
                sdc.ScheduleDayConfigId,
                sdc.EventTypeId,
                sdc.DayOfWeek,
                sdc.IsVisible
            FROM ScheduleDayConfig sdc
            WHERE 1 = 1
        ';
    }

    /** @param array<string,mixed> $params */
    private function buildEventTypeFilterClause(?int $eventTypeId, array &$params): string
    {
        if ($eventTypeId === null) {
            return '';
        }

        $params['eventTypeId'] = $eventTypeId;
        return ' AND sdc.EventTypeId = :eventTypeId';
    }

    private function resolveOrderClause(mixed $orderBy): string
    {
        $requestedOrder = is_string($orderBy) ? $orderBy : 'scope';
        $allowedOrders = [
            'scope' => ' ORDER BY (sdc.EventTypeId = 0) DESC, sdc.EventTypeId ASC, sdc.DayOfWeek ASC',
            'day' => ' ORDER BY sdc.DayOfWeek ASC',
        ];

        return $allowedOrders[$requestedOrder] ?? $allowedOrders['scope'];
    }

    // Unique key is (EventTypeId, DayOfWeek); repeated calls just flip IsVisible.
    public function upsert(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $this->execute(
            'INSERT INTO ScheduleDayConfig (EventTypeId, DayOfWeek, IsVisible)
            VALUES (:eventTypeId, :dayOfWeek, :isVisible)
            ON DUPLICATE KEY UPDATE IsVisible = :isVisible2',
            [
                'eventTypeId' => $eventTypeId ?? 0,
                'dayOfWeek' => $dayOfWeek,
                'isVisible' => $isVisible ? 1 : 0,
                'isVisible2' => $isVisible ? 1 : 0,
            ],
        );
    }
}
