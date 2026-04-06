<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Scanner;

/**
 * Ticket data enriched with event/session details for scanner display.
 */
final readonly class TicketScanDetail
{
    public function __construct(
        public int $ticketId,
        public string $ticketCode,
        public bool $isScanned,
        public ?string $scannedAtUtc,
        public string $eventTitle,
        public string $sessionDateTime,
        public int $durationMinutes,
        public string $venueName,
        public string $orderNumber,
    ) {
    }
}
