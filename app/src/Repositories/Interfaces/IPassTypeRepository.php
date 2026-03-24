<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PassType;

/**
 * Defines persistence operations for pass types (multi-session tickets) linked to event types.
 */
interface IPassTypeRepository
{
    /**
     * Returns all pass types available for the given event type.
     *
     * @return PassType[]
     */
    public function findByEventType(int $eventTypeId): array;
}
