<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionFilter;
use App\Models\ScheduleDayData;
use App\Models\SessionQueryResult;

/**
 * Defines persistence operations for event sessions (time slots).
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
}
