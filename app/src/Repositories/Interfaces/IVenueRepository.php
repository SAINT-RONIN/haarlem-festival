<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Venue;

interface IVenueRepository
{
    /**
     * @param array{isActive?: bool} $filters
     * @return Venue[]
     */
    public function findVenues(array $filters = []): array;

    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int;
}
