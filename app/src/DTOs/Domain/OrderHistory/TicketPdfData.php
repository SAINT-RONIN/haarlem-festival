<?php

declare(strict_types=1);

namespace App\DTOs\Domain\OrderHistory;

/**
 * Associates a ticket code with its generated PDF file path.
 * Used to build download links on the "My Orders" page for paid orders.
 */
final readonly class TicketPdfData
{
    public function __construct(
        public string $ticketCode,
        public string $filePath,
    ) {}

    /** Creates an instance from a raw database row. */
    public static function fromRow(array $row): self
    {
        return new self(
            ticketCode: (string) $row['TicketCode'],
            filePath: (string) $row['FilePath'],
        );
    }
}
