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
     * Returns all event types.
     *
     * @return EventType[]
     */
    public function findAll(): array;

    /**
     * Returns a single event type by ID.
     *
     * @param int $eventTypeId
     * @return EventType|null
     */
    public function findById(int $eventTypeId): ?EventType;

    /**
     * Returns all event types for dropdown.
     *
     * @return EventType[]
     */
    public function findAllForDropdown(): array;
}
