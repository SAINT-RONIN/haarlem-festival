<?php

declare(strict_types=1);

namespace App\DTOs\Tickets;

/**
 * Render-ready data for a single ticket PDF document.
 */
final readonly class TicketDocumentData
{
    public function __construct(
        public string $ticketCode,
        public string $ticketLabel,
        public string $orderReference,
        public string $recipientName,
        public string $eventTitle,
        public string $eventTypeName,
        public string $venueName,
        public string $sessionDateLabel,
        public string $sessionTimeLabel,
    ) {
    }
}
