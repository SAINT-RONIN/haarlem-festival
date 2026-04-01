<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Reservation` SQL table.
 */
class Reservation
{
    public function __construct(
        public readonly int     $eventId,
        public readonly string  $diningDate,
        public readonly string  $timeSlot,
        public readonly int     $adultsCount,
        public readonly int     $childrenCount,
        public readonly string  $specialRequests,
        public readonly float   $totalFee,
        public readonly ?int    $reservationId = null,
        // Populated by JOIN queries (not a DB column on Reservation)
        public readonly ?string $restaurantName = null,
        public readonly ?string $restaurantAddress = null,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            eventId:           (int)($row['EventId'] ?? $row['RestaurantId']),
            diningDate:        (string)$row['DiningDate'],
            timeSlot:          (string)$row['TimeSlot'],
            adultsCount:       (int)$row['AdultsCount'],
            childrenCount:     (int)$row['ChildrenCount'],
            specialRequests:   (string)($row['SpecialRequests'] ?? ''),
            totalFee:          (float)$row['TotalFee'],
            reservationId:     isset($row['ReservationId']) ? (int)$row['ReservationId'] : null,
            restaurantName:    isset($row['RestaurantName']) ? (string)$row['RestaurantName'] : null,
            restaurantAddress: isset($row['RestaurantAddress']) ? (string)$row['RestaurantAddress'] : null,
        );
    }
}