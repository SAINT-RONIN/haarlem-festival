<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
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
     * @return array Array of EventType rows
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT EventTypeId, Name, Slug
            FROM EventType
            ORDER BY EventTypeId ASC
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Returns a single event type by ID.
     *
     * @param int $eventTypeId
     * @return array|null EventType row or null if not found
     */
    public function findById(int $eventTypeId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT EventTypeId, Name, Slug
            FROM EventType
            WHERE EventTypeId = :eventTypeId
        ');
        $stmt->execute(['eventTypeId' => $eventTypeId]);

        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }
}
