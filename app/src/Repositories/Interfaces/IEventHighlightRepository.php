<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventHighlight;

/**
 * Contract for read-only access to event highlights -- short promotional blurbs
 * or key facts displayed on the event detail page, ordered by SortOrder.
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
