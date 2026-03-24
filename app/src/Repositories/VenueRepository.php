<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Venue;
use App\Models\VenueFilter;
use App\Repositories\Interfaces\IVenueRepository;
use PDO;

/**
 * Reads and creates records in the Venue table.
 *
 * Venues represent physical locations in Haarlem where festival events take place.
 * Supports optional active/inactive filtering for the venue list.
 */
class VenueRepository implements IVenueRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([Venue::class, 'fromRow'], $rows);
    }

    /**
     * Inserts a new venue (defaults to Haarlem) and returns the new VenueId.
     */
    public function create(string $name, string $addressLine, string $city = 'Haarlem'): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Venue (Name, AddressLine, City, IsActive)
            VALUES (:name, :addressLine, :city, 1)
        ');
        $stmt->execute([
            'name' => $name,
            'addressLine' => $addressLine,
            'city' => $city,
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
