<?php

declare(strict_types=1);

namespace App\DTOs\Events;

/**
 * Read-only snapshot of a session's capacity state.
 * Used by the checkout flow to validate availability and enforce the single-ticket cap.
 */
final readonly class SessionCapacityInfo
{
    public function __construct(
        public int $eventSessionId,
        public int $capacityTotal,
        public int $soldSingleTickets,
        public int $soldReservedSeats,
    ) {}

    /**
     * Returns the total number of remaining seats (capacity minus all sold tickets).
     */
    public function getAvailableSeats(): int
    {
        return max(0, $this->capacityTotal - $this->soldSingleTickets - $this->soldReservedSeats);
    }

    /**
     * Creates an instance from a database row.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionId: (int)$row['EventSessionId'],
            capacityTotal: (int)$row['CapacityTotal'],
            soldSingleTickets: (int)$row['SoldSingleTickets'],
            soldReservedSeats: (int)$row['SoldReservedSeats'],
        );
    }
}
