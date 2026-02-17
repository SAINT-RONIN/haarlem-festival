<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventType;

/**
 * Interface for EventType repository.
 */
interface IEventTypeRepository
{
    /**
     * Returns event types using optional filters.
     *
     * @param array{eventTypeId?: int, orderBy?: string} $filters
     * @return EventType[]
     */
    public function findEventTypes(array $filters = []): array;
}
