<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venue;
use App\DTOs\Filters\VenueFilter;
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
        $this->execute(
            'INSERT INTO Venue (Name, AddressLine, City, IsActive)
            VALUES (:name, :addressLine, :city, 1)',
            ['name' => $name, 'addressLine' => $addressLine, 'city' => $city],
        );

        return (int)$this->pdo->lastInsertId();
    }
}
