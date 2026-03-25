<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Schedule\ScheduleDayData;
use App\DTOs\Events\SessionCapacityInfo;
use App\DTOs\Schedule\SessionQueryResult;

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

    /**
     * Returns the number of remaining seats for a session (total capacity minus sold tickets).
     * Used for availability display and pre-checkout validation.
     */
    public function getAvailableSeats(int $sessionId): int;

    /**
     * Returns capacity and ticket-sale counts for a session.
     * Used for pre-checkout validation including the single-ticket cap.
     */
    public function getCapacityInfo(int $sessionId): ?SessionCapacityInfo;

    /**
     * Atomically increments SoldSingleTickets for a session. Uses a WHERE guard to prevent
     * overselling: the UPDATE only succeeds if enough capacity remains. Returns true if the
     * reservation succeeded, false if insufficient capacity.
     */
    public function decrementCapacity(int $sessionId, int $quantity): bool;

    /**
     * Restores reserved capacity when an order is cancelled or expires.
     * Called during payment failure webhook or manual cancellation.
     */
    public function restoreCapacity(int $sessionId, int $quantity): void;
}
