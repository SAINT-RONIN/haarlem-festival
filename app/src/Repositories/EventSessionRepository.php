<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IEventSessionRepository;
use PDO;

/**
 * Repository for EventSession database operations.
 */
class EventSessionRepository implements IEventSessionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns upcoming sessions with event and type details.
     *
     * Joins EventSession with Event and EventType to get all needed data
     * for the homepage schedule display.
     *
     * @return array Array of session data with event title and type slug
     */
    public function findUpcomingWithDetails(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                es.EventSessionId,
                es.StartDateTime,
                es.EndDateTime,
                e.Title AS EventTitle,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            WHERE es.IsActive = 1 
              AND es.IsCancelled = 0
              AND e.IsActive = 1
            ORDER BY es.StartDateTime ASC
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
