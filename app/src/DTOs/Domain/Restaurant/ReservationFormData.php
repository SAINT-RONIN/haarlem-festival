<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Restaurant;

/**
 * Typed carrier for restaurant reservation form fields submitted by the customer.
 *
 * Replaces the raw array that was previously passed to submitReservation(),
 * giving each field a named property and centralising the normalisation logic.
 */
final readonly class ReservationFormData
{
    public function __construct(
        public string $diningDate,
        public string $timeSlot,
        public int $adultsCount,
        public int $childrenCount,
        public string $specialRequests,
    ) {}

    /**
     * Creates an instance from the raw POST/JSON body array.
     *
     * Values are trimmed, clamped to safe bounds, and cast to their expected types.
     */
    public static function fromArray(array $postData): self
    {
        return new self(
            diningDate: trim((string)($postData['dining_date'] ?? '')),
            timeSlot: trim((string)($postData['time_slot'] ?? '')),
            adultsCount: max(0, (int)($postData['adults_count'] ?? 0)),
            childrenCount: max(0, (int)($postData['children_count'] ?? 0)),
            specialRequests: trim((string)($postData['special_requests'] ?? '')),
        );
    }

    /** Returns the total number of guests (adults + children). */
    public function totalGuests(): int
    {
        return $this->adultsCount + $this->childrenCount;
    }
}
