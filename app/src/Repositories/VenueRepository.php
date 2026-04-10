<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venue;
use App\DTOs\Domain\Filters\VenueFilter;
use App\Repositories\Interfaces\IVenueRepository;

class VenueRepository extends BaseRepository implements IVenueRepository
{
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

    // Defaults city to 'Haarlem' when omitted.
    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int
    {
        return $this->executeInsert(
            'INSERT INTO Venue (Name, AddressLine, City, IsActive)
            VALUES (:name, :addressLine, :city, 1)',
            ['name' => $name, 'addressLine' => $addressLine, 'city' => $city],
        );
    }

    public function findById(int $venueId): ?Venue
    {
        $rows = $this->fetchAll(
            'SELECT VenueId, Name, AddressLine, City, CreatedAtUtc, IsActive FROM Venue WHERE VenueId = :venueId',
            ['venueId' => $venueId],
            fn(array $row) => Venue::fromRow($row),
        );
        return $rows[0] ?? null;
    }

    public function softDelete(int $venueId): bool
    {
        $statement = $this->execute(
            'UPDATE Venue SET IsActive = 0 WHERE VenueId = :venueId',
            ['venueId' => $venueId],
        );

        return $statement->rowCount() > 0;
    }

    // Case-insensitive to prevent near-duplicate entries.
    public function existsByName(string $name): bool
    {
        $rows = $this->fetchAll(
            'SELECT 1 FROM Venue WHERE LOWER(Name) = LOWER(:name) AND IsActive = 1 LIMIT 1',
            ['name' => $name],
            fn(array $row) => $row,
        );

        return $rows !== [];
    }
}
