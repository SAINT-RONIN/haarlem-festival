<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Event;

interface IEventRepository
{
    /**
     * @param array{
     *   eventTypeId?: int,
     *   dayOfWeek?: string,
     *   isActive?: bool,
     *   includeSessionCount?: bool,
     *   eventId?: int
     * } $filters
     * @return array<int, array<string, mixed>>
     */
    public function findEvents(array $filters = []): array;

    public function findById(int $eventId): ?Event;

    public function create(array $data): int;

    public function update(int $eventId, array $data): bool;

    public function delete(int $eventId): bool;

    public function exists(int $eventId): bool;

    public function softDelete(int $eventId): bool;

    public function deactivateSessions(int $eventId): bool;
}
