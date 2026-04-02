<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Creates paid-order tickets, generates PDFs, and emails them to the checkout recipient.
 */
interface ITicketFulfillmentService
{
    public function fulfillPaidOrder(
        int $orderId,
        ?string $fallbackEmail = null,
        ?string $fallbackFirstName = null,
        ?string $fallbackLastName = null,
    ): void;

    public function regenerateTicketDocumentsByTicketCode(string $ticketCode): void;
}
