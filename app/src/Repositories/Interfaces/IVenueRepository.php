<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for Venue repository.
 */
interface IVenueRepository
{
    /**
     * Returns all active venues.
     *
     * @return array Array of Venue data
     */
    public function findAllActive(): array;
}
