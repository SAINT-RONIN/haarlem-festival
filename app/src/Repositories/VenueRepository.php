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
}

