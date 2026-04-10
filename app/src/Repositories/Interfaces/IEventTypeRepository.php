<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventType;
use App\DTOs\Domain\Filters\EventTypeFilter;

/**
 * Contract for accessing the EventType lookup table. Event types categorise festival
 * events (e.g. "Jazz", "Dance", "Food") and are referenced by Event rows and schedule config.
 */
interface IEventTypeRepository
{
    /**
     * Retrieves event types with optional ID filter and configurable sort order.
     *
     * @return EventType[]
     */
    public function findEventTypes(EventTypeFilter $filter = new EventTypeFilter()): array;
}
