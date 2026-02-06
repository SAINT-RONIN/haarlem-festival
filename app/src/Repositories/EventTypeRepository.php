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
}
