<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Reservation;
use PDO;

/**
 * Repository for Reservation database operations.
 */
class ReservationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Inserts a new reservation and returns its generated ID.
     */
    public function insert(Reservation $r): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Reservation
                (RestaurantId, DiningDate, TimeSlot, AdultsCount, ChildrenCount, SpecialRequests, TotalFee)
            VALUES
                (:restaurantId, :diningDate, :timeSlot, :adultsCount, :childrenCount, :specialRequests, :totalFee)
        ');

        $stmt->execute([
            'restaurantId'    => $r->restaurantId,
            'diningDate'      => $r->diningDate,
            'timeSlot'        => $r->timeSlot,
            'adultsCount'     => $r->adultsCount,
            'childrenCount'   => $r->childrenCount,
            'specialRequests' => $r->specialRequests,
            'totalFee'        => $r->totalFee,
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}