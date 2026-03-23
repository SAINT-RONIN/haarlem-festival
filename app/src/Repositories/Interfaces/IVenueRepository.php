<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Venue;
use App\Models\VenueFilter;

interface IVenueRepository
{
    /**
     * @return Venue[]
     */
    public function findVenues(VenueFilter $filter = new VenueFilter()): array;

    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int;
}
