<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventHighlight;

/**
 * Defines persistence operations for event highlights.
 */
interface IEventHighlightRepository
{
    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return EventHighlight[]
     */
    public function findByEventId(int $eventId): array;
}
