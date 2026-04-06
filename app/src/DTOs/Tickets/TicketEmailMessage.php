<?php

declare(strict_types=1);

namespace App\DTOs\Tickets;

/**
 * Structured payload for sending paid-order ticket emails.
 *
 * @param string[] $eventSummaryLines
 * @param TicketEmailAttachment[] $attachments
 */
final readonly class TicketEmailMessage
{
    /**
     * @param string[] $eventSummaryLines
     * @param TicketEmailAttachment[] $attachments
     */
    public function __construct(
        public string $toEmail,
        public string $recipientName,
        public string $orderReference,
        public int $ticketCount,
        public array $eventSummaryLines,
        public array $attachments,
    ) {
    }
}
