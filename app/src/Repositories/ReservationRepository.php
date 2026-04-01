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
                (RestaurantId, DiningDate, TimeSlot, AdultsCount, ChildrenCount, SpecialRequests, TotalFee)
            VALUES
                (:restaurantId, :diningDate, :timeSlot, :adultsCount, :childrenCount, :specialRequests, :totalFee)',
            [
                'restaurantId' => $r->restaurantId,
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
     * Fetches a reservation by ID, JOINed with the Restaurant table for name/address display.
     */
    public function findWithRestaurant(int $reservationId): ?Reservation
    {
        return $this->fetchOne(
            'SELECT r.*,
                   rest.Name AS RestaurantName,
                   CONCAT(rest.AddressLine, \', \', rest.City) AS RestaurantAddress
            FROM Reservation r
            JOIN Restaurant rest ON rest.RestaurantId = r.RestaurantId
            WHERE r.ReservationId = :reservationId',
            ['reservationId' => $reservationId],
            fn(array $row) => Reservation::fromRow($row),
        );
    }
}
