<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventType;
use App\Models\EventTypeFilter;

/**
 * Interface for EventType repository.
 */
interface IEventTypeRepository
{
    /**
     * Returns event types using optional filters.
     *
     * @return EventType[]
     */
    public function findEventTypes(EventTypeFilter $filter = new EventTypeFilter()): array;
}
