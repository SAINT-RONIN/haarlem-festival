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

    /**
     * Clears any prior send state and re-runs the full fulfillment flow for the order.
     *
     * Unlike fulfillPaidOrder(), this ignores the idempotency guard so admins can
     * force a resend even when the email was already delivered once. All existing
     * ticket rows and PDFs are reused; only the email delivery step runs unconditionally.
     *
     * @throws \App\Exceptions\TicketDeliveryException When the order cannot be found or has no ticketable items.
     * @throws \App\Exceptions\TicketEmailDeliveryException When SMTP delivery fails.
     */
    public function resendTicketEmailForOrder(int $orderId): void;
}
