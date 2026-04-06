<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venue;
use App\DTOs\Domain\Filters\VenueFilter;
use App\Repositories\Interfaces\IVenueRepository;

/**
 * Reads and creates records in the Venue table.
 *
 * Venues represent physical locations in Haarlem where festival events take place.
 * Supports optional active/inactive filtering for the venue list.
 */
class VenueRepository extends BaseRepository implements IVenueRepository
{
    /**
     * Returns venues ordered by name, optionally filtered by active status.
     *
     * @return Venue[]
     */
    public function findVenues(VenueFilter $filter = new VenueFilter()): array
    {
        $sql = '
            SELECT VenueId, Name, AddressLine, City, CreatedAtUtc, IsActive
            FROM Venue
            WHERE 1 = 1
        ';

        $params = [];

        if ($filter->isActive !== null) {
            $sql .= ' AND IsActive = :isActive';
            $params['isActive'] = $filter->isActive ? 1 : 0;
        }

        $sql .= ' ORDER BY Name ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => Venue::fromRow($row));
    }

    /**
     * Inserts a new venue (defaults to Haarlem) and returns the new VenueId.
     */
    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int
    {
        return $this->executeInsert(
            'INSERT INTO Venue (Name, AddressLine, City, IsActive)
            VALUES (:name, :addressLine, :city, 1)',
            ['name' => $name, 'addressLine' => $addressLine, 'city' => $city],
        );
    }

    /**
     * Finds a single venue by its primary key, or null if not found.
     */
    public function findById(int $venueId): ?Venue
    {
        $rows = $this->fetchAll(
            'SELECT VenueId, Name, AddressLine, City, CreatedAtUtc, IsActive FROM Venue WHERE VenueId = :venueId',
            ['venueId' => $venueId],
            fn(array $row) => Venue::fromRow($row),
        );
        return $rows[0] ?? null;
    }

    /**
     * Soft-deletes a venue by setting IsActive = 0.
     */
    public function softDelete(int $venueId): bool
    {
        $statement = $this->execute(
            'UPDATE Venue SET IsActive = 0 WHERE VenueId = :venueId',
            ['venueId' => $venueId],
        );

        return $statement->rowCount() > 0;
    }
}
