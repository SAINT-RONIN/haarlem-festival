<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionFilter;

interface IEventSessionRepository
{
    /**
     * @param EventSessionFilter|array<string, mixed> $filters
     * @return array{days?: \App\Models\ScheduleDayData[], sessions: \App\Models\SessionWithEvent[]}
     */
    public function findSessions(EventSessionFilter|array $filters = new EventSessionFilter()): array;

    public function create(array $data): int;

    public function update(int $sessionId, array $data): bool;

    public function delete(int $sessionId): bool;
}
