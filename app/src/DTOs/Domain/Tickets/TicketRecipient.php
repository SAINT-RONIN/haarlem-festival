<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Tickets;

/**
 * Checkout recipient details used for ticket delivery.
 */
final readonly class TicketRecipient
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $displayName,
    ) {}
}
