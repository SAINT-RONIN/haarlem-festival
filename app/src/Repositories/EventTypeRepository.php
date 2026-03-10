<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventType;
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

    public function findEventTypes(array $filters = []): array
    {
        $sql = '
            SELECT EventTypeId, Name, Slug
            FROM EventType
            WHERE 1 = 1
        ';
        $params = [];

        if (isset($filters['eventTypeId'])) {
            $sql .= ' AND EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$filters['eventTypeId'];
        }

        $orderByFilter = is_string($filters['orderBy'] ?? null)
            ? strtolower((string)$filters['orderBy'])
            : 'id';
        $sql .= $orderByFilter === 'name'
            ? ' ORDER BY Name ASC'
            : ' ORDER BY EventTypeId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventType::class, 'fromRow'], $rows);
    }
}
