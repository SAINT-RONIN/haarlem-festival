<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Venue;
use App\Models\VenueFilter;

/**
 * Defines persistence operations for festival venues.
 */
interface IVenueRepository
{
    /**
     * Queries venues using optional filters.
     *
     * @return Venue[]
     */
    public function findVenues(VenueFilter $filter = new VenueFilter()): array;

    /**
     * Inserts a new venue and returns the generated ID.
     */
    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int;
}
