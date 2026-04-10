<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

/**
 * Read-only snapshot of a session's capacity state.
 *
 * Used by the checkout flow and program service to validate availability
 * and enforce the single-ticket cap. All values come from the EventSession
 * table and are configurable per-session in the CMS.
 */
final readonly class SessionCapacityInfo
{
    public function __construct(
        public int $eventSessionId,
        public int $capacityTotal,
        public int $soldSingleTickets,
        public int $soldReservedSeats,
        public int $capacitySingleTicketLimit,
    ) {}

    /**
     * Creates an instance from a database row.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionId: (int) $row['EventSessionId'],
            capacityTotal: (int) $row['CapacityTotal'],
            soldSingleTickets: (int) $row['SoldSingleTickets'],
            soldReservedSeats: (int) $row['SoldReservedSeats'],
            capacitySingleTicketLimit: (int) ($row['CapacitySingleTicketLimit'] ?? 0),
        );
    }

    /**
     * Returns the number of seats currently available for booking.
     */
    public function availableSeats(): int
    {
        return max(0, $this->capacityTotal - $this->soldSingleTickets - $this->soldReservedSeats);
    }
}
