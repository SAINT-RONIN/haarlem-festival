<?php

declare(strict_types=1);

namespace App\DTOs\Tickets;

/**
 * Result of a ticket scan attempt.
 */
final readonly class TicketScanResult
{
    public function __construct(
        public string $ticketCode,
        public bool $isScanned,
        public string $message,
        public string $scannedAtLabel,
    ) {
    }
}
