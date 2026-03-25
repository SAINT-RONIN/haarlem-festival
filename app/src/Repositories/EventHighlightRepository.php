<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventHighlight;
use App\Repositories\Interfaces\IEventHighlightRepository;
use PDO;

/**
 * Read-only access to the EventHighlight table.
 *
 * Event highlights are short promotional blurbs or key facts displayed
 * on the event detail page, ordered by SortOrder.
 */
class EventHighlightRepository implements IEventHighlightRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return EventHighlight[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM EventHighlight
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventHighlight::class, 'fromRow'], $rows);
    }
}
