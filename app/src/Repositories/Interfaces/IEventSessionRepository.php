<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for EventSession repository.
 */
interface IEventSessionRepository
{
    /**
     * Returns upcoming sessions with event and type details.
     *
     * @return array Array of session data with joined event/type info
     */
    public function findUpcomingWithDetails(): array;
}
