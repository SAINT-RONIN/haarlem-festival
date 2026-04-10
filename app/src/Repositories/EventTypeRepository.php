<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventType;
use App\DTOs\Domain\Filters\EventTypeFilter;
use App\Repositories\Interfaces\IEventTypeRepository;

class EventTypeRepository extends BaseRepository implements IEventTypeRepository
{
    public function findEventTypes(EventTypeFilter $filter = new EventTypeFilter()): array
    {
        $sql = 'SELECT EventTypeId, Name, Slug FROM EventType WHERE 1 = 1';
        $params = [];

        if ($filter->eventTypeId !== null) {
            $sql .= ' AND EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int) $filter->eventTypeId;
        }

        $orderBy = is_string($filter->orderBy) ? strtolower($filter->orderBy) : 'id';
        $sql .= $orderBy === 'name'
            ? ' ORDER BY Name ASC'
            : ' ORDER BY EventTypeId ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => EventType::fromRow($row));
    }
}
