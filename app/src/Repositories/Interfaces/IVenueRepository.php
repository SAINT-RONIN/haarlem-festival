<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Venue;

/**
 * Interface for Venue repository.
 */
interface IVenueRepository
{
    /**
     * Returns all active venues.
     *
     * @return Venue[]
     */
    public function findAllActive(): array;

    /**
     * Returns all active venues for dropdown.
     *
     * @return Venue[]
     */
    public function findAllForDropdown(): array;
}
