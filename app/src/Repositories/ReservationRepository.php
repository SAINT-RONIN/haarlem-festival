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
                (EventId, DiningDate, TimeSlot, AdultsCount, ChildrenCount, SpecialRequests, TotalFee)
            VALUES
                (:eventId, :diningDate, :timeSlot, :adultsCount, :childrenCount, :specialRequests, :totalFee)
        ');

        $stmt->execute([
            'eventId'         => $r->eventId,
            'diningDate'      => $r->diningDate,
            'timeSlot'        => $r->timeSlot,
            'adultsCount'     => $r->adultsCount,
            'childrenCount'   => $r->childrenCount,
            'specialRequests' => $r->specialRequests,
            'totalFee'        => $r->totalFee,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Fetches a reservation by ID, JOINed with the Event table for name display.
     */
    public function findWithRestaurant(int $reservationId): ?Reservation
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, e.Title AS RestaurantName
            FROM Reservation r
            JOIN Event e ON e.EventId = r.EventId
            WHERE r.ReservationId = :reservationId
        ');

        $stmt->execute(['reservationId' => $reservationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? Reservation::fromRow($row) : null;
    }
}