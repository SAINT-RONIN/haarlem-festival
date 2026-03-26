<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventHighlight;
use App\Repositories\Interfaces\IEventHighlightRepository;

/**
 * Read-only access to the EventHighlight table.
 *
 * Event highlights are short promotional blurbs or key facts displayed
 * on the event detail page, ordered by SortOrder.
 */
class EventHighlightRepository extends BaseRepository implements IEventHighlightRepository
{
    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return EventHighlight[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM EventHighlight WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => EventHighlight::fromRow($row),
        );
    }
}
