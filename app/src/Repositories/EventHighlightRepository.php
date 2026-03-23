<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventHighlight;
use App\Repositories\Interfaces\IEventHighlightRepository;
use PDO;

/**
 * Repository for EventHighlight database operations.
 */
class EventHighlightRepository implements IEventHighlightRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return EventHighlight[]
     */
    public function findByEventId(int $eventId): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM EventHighlight
                WHERE EventId = :eventId
                ORDER BY SortOrder ASC
            ');
            $stmt->execute(['eventId' => $eventId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([EventHighlight::class, 'fromRow'], $rows);
        } catch (\PDOException) {
            return [];
        }
    }
}
