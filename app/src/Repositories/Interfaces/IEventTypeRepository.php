<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for EventType repository.
 */
interface IEventTypeRepository
{
    /**
     * Returns all event types.
     *
     * @return array Array of EventType data
     */
    public function findAll(): array;
}
