<?php

declare(strict_types=1);

namespace App\DTOs\Tickets;

/**
 * File attachment metadata for outbound ticket emails.
 */
final readonly class TicketEmailAttachment
{
    public function __construct(
        public string $absolutePath,
        public string $displayName,
    ) {
    }
}
