<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;

/**
 * Represents a row in the Order table.
 *
 * Created during checkout when a visitor purchases tickets. Tracks order status
 * (Pending -> Paid/Cancelled/Expired) and total amounts including VAT.
 */
final readonly class Order
{
    /*
     * Purpose: Stores order header data including status, totals,
     * and payment deadline for ticket purchases.
     */

    public function __construct(
        public int                 $orderId,
        public string              $orderNumber,
        public int                 $userAccountId,
        public int                 $programId,
        public OrderStatus         $status,
        public \DateTimeImmutable  $createdAtUtc,
        public ?\DateTimeImmutable $payBeforeUtc,
        public string              $subtotal,
        public string              $vatTotal,
        public string              $totalAmount,
        public ?string             $ticketRecipientFirstName,
        public ?string             $ticketRecipientLastName,
        public ?string             $ticketRecipientEmail,
        public ?\DateTimeImmutable $ticketEmailSentAtUtc,
        public ?string             $ticketEmailLastError,
    ) {
    }

    /**
     * Creates an Order instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            orderId: (int)$row['OrderId'],
            orderNumber: (string)$row['OrderNumber'],
            userAccountId: (int)$row['UserAccountId'],
            programId: (int)$row['ProgramId'],
            status: OrderStatus::from($row['Status']),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            payBeforeUtc: isset($row['PayBeforeUtc']) ? new \DateTimeImmutable($row['PayBeforeUtc']) : null,
            subtotal: (string)$row['Subtotal'],
            vatTotal: (string)$row['VatTotal'],
            totalAmount: (string)$row['TotalAmount'],
            ticketRecipientFirstName: isset($row['TicketRecipientFirstName']) ? (string)$row['TicketRecipientFirstName'] : null,
            ticketRecipientLastName: isset($row['TicketRecipientLastName']) ? (string)$row['TicketRecipientLastName'] : null,
            ticketRecipientEmail: isset($row['TicketRecipientEmail']) ? (string)$row['TicketRecipientEmail'] : null,
            ticketEmailSentAtUtc: isset($row['TicketEmailSentAtUtc']) && $row['TicketEmailSentAtUtc'] !== null
                ? new \DateTimeImmutable($row['TicketEmailSentAtUtc'])
                : null,
            ticketEmailLastError: isset($row['TicketEmailLastError']) ? (string)$row['TicketEmailLastError'] : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'OrderId' => $this->orderId,
            'OrderNumber' => $this->orderNumber,
            'UserAccountId' => $this->userAccountId,
            'ProgramId' => $this->programId,
            'Status' => $this->status->value,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'PayBeforeUtc' => $this->payBeforeUtc?->format('Y-m-d H:i:s'),
            'Subtotal' => $this->subtotal,
            'VatTotal' => $this->vatTotal,
            'TotalAmount' => $this->totalAmount,
            'TicketRecipientFirstName' => $this->ticketRecipientFirstName,
            'TicketRecipientLastName' => $this->ticketRecipientLastName,
            'TicketRecipientEmail' => $this->ticketRecipientEmail,
            'TicketEmailSentAtUtc' => $this->ticketEmailSentAtUtc?->format('Y-m-d H:i:s'),
            'TicketEmailLastError' => $this->ticketEmailLastError,
        ];
    }
}
