<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IVenueRepository;
use PDO;

/**
 * Repository for Venue database operations.
 */
class VenueRepository implements IVenueRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all active venues.
     *
     * @return array Array of Venue rows
     */
    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT VenueId, Name, AddressLine, City
            FROM Venue
            WHERE IsActive = 1
            ORDER BY Name ASC
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Returns all active venues for dropdown (VenueId, Name, AddressLine).
     */
    public function findAllForDropdown(): array
    {
        $stmt = $this->pdo->query('SELECT VenueId, Name, AddressLine FROM Venue WHERE IsActive = 1 ORDER BY Name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Creates a new venue.
     *
     * @return int The new venue ID
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
