<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Read-only projection of a single order with user/recipient details for the CMS detail page.
 */
final readonly class CmsOrderDetailDto
{
    public function __construct(
        public int     $orderId,
        public string  $orderNumber,
        public string  $status,
        public string  $totalAmount,
        public string  $subtotal,
        public string  $vatTotal,
        public string  $createdAtUtc,
        public string  $payBeforeUtc,
        public string  $userEmail,
        public string  $ticketRecipientFirstName,
        public string  $ticketRecipientLastName,
        public string  $ticketRecipientEmail,
        public ?string $ticketEmailSentAtUtc,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            orderId:                 (int) $row['OrderId'],
            orderNumber:             (string) $row['OrderNumber'],
            status:                  (string) $row['Status'],
            totalAmount:             (string) $row['TotalAmount'],
            subtotal:                (string) ($row['Subtotal'] ?? '0.00'),
            vatTotal:                (string) ($row['VatTotal'] ?? '0.00'),
            createdAtUtc:            (string) $row['CreatedAtUtc'],
            payBeforeUtc:            (string) ($row['PayBeforeUtc'] ?? ''),
            userEmail:               (string) ($row['UserEmail'] ?? ''),
            ticketRecipientFirstName:(string) ($row['TicketRecipientFirstName'] ?? ''),
            ticketRecipientLastName: (string) ($row['TicketRecipientLastName'] ?? ''),
            ticketRecipientEmail:    (string) ($row['TicketRecipientEmail'] ?? ''),
            ticketEmailSentAtUtc:    isset($row['TicketEmailSentAtUtc']) ? (string) $row['TicketEmailSentAtUtc'] : null,
        );
    }
}
