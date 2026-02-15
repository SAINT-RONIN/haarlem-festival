<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Venue;
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
     * Returns all active venues as Venue models.
     *
     * @return Venue[]
     */
    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT VenueId, Name, AddressLine, City, CreatedAtUtc, IsActive
            FROM Venue
            WHERE IsActive = 1
            ORDER BY Name ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Venue::class, 'fromRow'], $rows);
    }

    /**
     * Returns all active venues for dropdown (VenueId, Name, AddressLine).
     *
     * Returns array for lightweight dropdown population.
     *
     * @return array<int, array{VenueId: int, Name: string, AddressLine: string}>
     */
    public function findAllForDropdown(): array
    {
        $stmt = $this->pdo->query('SELECT VenueId, Name, AddressLine FROM Venue WHERE IsActive = 1 ORDER BY Name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
