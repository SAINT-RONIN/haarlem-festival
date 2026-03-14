<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IEventSessionRepository
{
    /**
     * @param array{
     *   eventId?: int,
     *   eventTypeId?: int,
     *   sessionId?: int,
     *   sessionIds?: int[],
     *   isActive?: bool,
     *   includeCancelled?: bool,
     *   orderBy?: string,
     *   maxDays?: int,
     *   startDate?: string,
     *   endDate?: string,
     *   visibleDays?: array<int>,
     *   groupByDay?: bool,
     *   includeEventType?: bool,
     *   includeVenue?: bool,
     *   includeArtist?: bool
     * } $filters
     * @return array<string, mixed>
     */
    public function findSessions(array $filters = []): array;

    public function create(array $data): int;

    public function update(int $sessionId, array $data): bool;

    public function delete(int $sessionId): bool;
}
