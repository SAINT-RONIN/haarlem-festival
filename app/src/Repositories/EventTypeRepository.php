<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventType;
use App\Models\EventTypeFilter;
use App\Repositories\Interfaces\IEventTypeRepository;
use PDO;

/**
 * Repository for EventType database operations.
 */
class EventTypeRepository implements IEventTypeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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
