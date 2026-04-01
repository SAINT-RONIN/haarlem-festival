<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Reservation` SQL table.
 */
final readonly class Reservation
{
    public function __construct(
        public int     $restaurantId,
        public string  $diningDate,
        public string  $timeSlot,
        public int     $adultsCount,
        public int     $childrenCount,
        public string  $specialRequests,
        public float   $totalFee,
        public ?int    $reservationId = null,
        // Populated by JOIN queries (not a DB column on Reservation)
        public ?string $restaurantName = null,
        public ?string $restaurantAddress = null,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            restaurantId:      (int)$row['RestaurantId'],
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