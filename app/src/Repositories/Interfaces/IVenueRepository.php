<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Venue;
use App\DTOs\Filters\VenueFilter;

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

    /**
     * Finds a single venue by its primary key, or null if not found.
     */
    public function findById(int $venueId): ?Venue;

    /**
     * Soft-deletes a venue by setting IsActive = 0.
     */
    public function softDelete(int $venueId): bool;
}
