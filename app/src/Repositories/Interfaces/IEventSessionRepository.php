<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionFilter;
use App\Models\ScheduleDayData;
use App\Models\SessionQueryResult;

/**
 * Contract for managing event sessions (individual bookable time slots within an event).
 * Supports heavily-filtered queries with joins to Event, EventType, Venue, and Artist,
 * plus day-grouped results for the public schedule UI.
 */
interface IEventSessionRepository
{
    /**
     * Queries sessions with optional filters, returning a result set that includes pagination metadata.
     *
     * @return SessionQueryResult
     */
    public function findSessions(EventSessionFilter $filters = new EventSessionFilter()): SessionQueryResult;

    /**
     * Returns distinct session dates for building filter UI.
     *
     * @return ScheduleDayData[]
     */
    public function findDistinctDays(EventSessionFilter $filter): array;

    /**
     * Inserts a new event session and returns the generated ID.
     */
    public function create(array $data): int;

    /**
     * Updates an event session's columns and returns whether any row was affected.
     */
    public function update(int $sessionId, array $data): bool;

    /**
     * Deletes an event session by its ID.
     */
    public function delete(int $sessionId): bool;

    /**
     * Bulk-deactivates all sessions belonging to an event.
     */
    public function deactivateByEventId(int $eventId): bool;
}
