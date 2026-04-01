<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Reservation;

/**
 * Contract for reservation persistence operations.
 */
interface IReservationRepository
{
    /**
     * Inserts a new reservation and returns the generated ID.
     */
    public function insert(Reservation $r): int;

    /**
     * Fetches a reservation by ID with restaurant name/address populated.
     */
    public function findWithRestaurant(int $reservationId): ?Reservation;
}
