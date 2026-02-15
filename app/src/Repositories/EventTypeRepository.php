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

    /**
     * Returns all event types.
     *
     * @return EventType[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT EventTypeId, Name, Slug
            FROM EventType
            ORDER BY EventTypeId ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventType::class, 'fromRow'], $rows);
    }

    /**
     * Returns a single event type by ID.
     *
     * @param int $eventTypeId
     * @return EventType|null
     */
    public function findById(int $eventTypeId): ?EventType
    {
        $stmt = $this->pdo->prepare('
            SELECT EventTypeId, Name, Slug
            FROM EventType
            WHERE EventTypeId = :eventTypeId
        ');
        $stmt->execute(['eventTypeId' => $eventTypeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? EventType::fromRow($result) : null;
    }

    /**
     * Returns all event types for dropdown.
     *
     * @return EventType[]
     */
    public function findAllForDropdown(): array
    {
        $stmt = $this->pdo->query('SELECT EventTypeId, Name, Slug FROM EventType ORDER BY Name ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventType::class, 'fromRow'], $rows);
    }
}
