<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventType;
use App\Models\EventTypeFilter;
use App\Repositories\Interfaces\IEventTypeRepository;
use PDO;

/**
 * Read-only access to the EventType lookup table.
 *
 * Event types categorise festival events (e.g. "Jazz", "Dance", "Food")
 * and are referenced by Event, ScheduleDayConfig, and PassType.
 */
class EventTypeRepository implements IEventTypeRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retrieves event types with optional ID filter and configurable sort order.
     *
     * @return EventType[]
     */
    public function findEventTypes(EventTypeFilter $filter = new EventTypeFilter()): array
    {
        $sql = '
            SELECT EventTypeId, Name, Slug
            FROM EventType
            WHERE 1 = 1
        ';
        $params = [];

        if ($filter->eventTypeId !== null) {
            $sql .= ' AND EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$filter->eventTypeId;
        }

        $orderBy = is_string($filter->orderBy) ? strtolower($filter->orderBy) : 'id';
        $sql .= $orderBy === 'name'
            ? ' ORDER BY Name ASC'
            : ' ORDER BY EventTypeId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventType::class, 'fromRow'], $rows);
    }
}
