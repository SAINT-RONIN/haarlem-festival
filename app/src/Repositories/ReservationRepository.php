<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reservation;
use App\Repositories\Interfaces\IReservationRepository;

class ReservationRepository extends BaseRepository implements IReservationRepository
{
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

    public function countBookedGuests(int $eventId, string $diningDate, string $timeSlot): int
    {
        $stmt = $this->execute(
            'SELECT COALESCE(SUM(AdultsCount + ChildrenCount), 0) AS TotalGuests
            FROM Reservation
            WHERE EventId = :eventId
              AND DiningDate = :diningDate
              AND TimeSlot = :timeSlot',
            [
                'eventId' => $eventId,
                'diningDate' => $diningDate,
                'timeSlot' => $timeSlot,
            ],
        );

        return (int) $stmt->fetchColumn();
    }

    // Joins Event to get the restaurant display name.
    public function findWithRestaurant(int $reservationId): ?Reservation
    {
        return $this->fetchOne(
            'SELECT r.*, e.Title AS RestaurantName,
                    CONCAT_WS(\', \', v.AddressLine, v.City) AS RestaurantAddress
            FROM Reservation r
            JOIN Event e ON e.EventId = r.EventId
            LEFT JOIN Venue v ON v.VenueId = e.VenueId
            WHERE r.ReservationId = :reservationId',
            ['reservationId' => $reservationId],
            fn(array $row) => Reservation::fromRow($row),
        );
    }
}
