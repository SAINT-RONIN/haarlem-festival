<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Ticket` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Ticket
{
    /*
     * Purpose: Stores individual tickets with unique codes for
     * entry validation and scanning at events.
     */

    public function __construct(
        public readonly int                 $ticketId,
        public readonly int                 $orderItemId,
        public readonly string              $ticketCode,
        public readonly bool                $isScanned,
        public readonly ?\DateTimeImmutable $scannedAtUtc,
        public readonly ?int                $scannedByUserId,
        public readonly ?int                $pdfAssetId,
    )
    {
    }

    /**
     * Creates a Ticket instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            ticketId: (int)$row['TicketId'],
            orderItemId: (int)$row['OrderItemId'],
            ticketCode: (string)$row['TicketCode'],
            isScanned: (bool)$row['IsScanned'],
            scannedAtUtc: isset($row['ScannedAtUtc']) ? new \DateTimeImmutable($row['ScannedAtUtc']) : null,
            scannedByUserId: isset($row['ScannedByUserId']) ? (int)$row['ScannedByUserId'] : null,
            pdfAssetId: isset($row['PdfAssetId']) ? (int)$row['PdfAssetId'] : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'TicketId' => $this->ticketId,
            'OrderItemId' => $this->orderItemId,
            'TicketCode' => $this->ticketCode,
            'IsScanned' => $this->isScanned,
            'ScannedAtUtc' => $this->scannedAtUtc?->format('Y-m-d H:i:s'),
            'ScannedByUserId' => $this->scannedByUserId,
            'PdfAssetId' => $this->pdfAssetId,
        ];
    }
}
