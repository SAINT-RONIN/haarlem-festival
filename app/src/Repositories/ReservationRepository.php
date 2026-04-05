<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reservation;
use App\Repositories\Interfaces\IReservationRepository;

/**
 * Manages persistence operations on the Reservation table.
 */
class ReservationRepository extends BaseRepository implements IReservationRepository
{
    /**
     * Inserts a new reservation and returns its generated ID.
     */
    public function insert(Reservation $r): int
    {
        return $this->executeInsert(
            'INSERT INTO Reservation
                (EventId, DiningDate, TimeSlot, AdultsCount, ChildrenCount, SpecialRequests, TotalFee)
            VALUES
                (:eventId, :diningDate, :timeSlot, :adultsCount, :childrenCount, :specialRequests, :totalFee)',
            [
                'eventId' => $r->eventId,
                'diningDate' => $r->diningDate,
                'timeSlot' => $r->timeSlot,
                'adultsCount' => $r->adultsCount,
                'childrenCount' => $r->childrenCount,
                'specialRequests' => $r->specialRequests,
                'totalFee' => $r->totalFee,
            ],
        );
    }

    /**
     * Fetches a reservation by ID with display name from the linked Event.
     */
    public function findWithRestaurant(int $reservationId): ?Reservation
    {
        return $this->fetchOne(
            'SELECT r.*, e.Title AS RestaurantName, NULL AS RestaurantAddress
            FROM Reservation r
            JOIN Event e ON e.EventId = r.EventId
            WHERE r.ReservationId = :reservationId',
            ['reservationId' => $reservationId],
            fn(array $row) => Reservation::fromRow($row),
        );
    }
}
