<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionFilter;
use App\Models\ScheduleDayData;
use App\Models\SessionQueryResult;

interface IEventSessionRepository
{
    /**
     * @return SessionQueryResult
     */
    public function findSessions(EventSessionFilter $filters = new EventSessionFilter()): SessionQueryResult;

    /**
     * Returns distinct session dates for building filter UI.
     *
     * @return ScheduleDayData[]
     */
    public function findDistinctDays(EventSessionFilter $filter): array;

    public function create(array $data): int;

    public function update(int $sessionId, array $data): bool;

    public function delete(int $sessionId): bool;

    /**
     * Increments SoldReservedSeats for the EventSession matching the given
     * event, ISO date (e.g. '2026-07-23'), and time slot (e.g. '16:30').
     */
    public function incrementSoldReservedSeats(int $eventId, string $isoDate, string $timeSlot, int $count): void;
}
