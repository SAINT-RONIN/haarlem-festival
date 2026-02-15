<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ProgramItem` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class ProgramItem
{
    /*
     * Purpose: Stores individual items in a user's program/cart
     * (tickets, passes, tours) before checkout.
     */

    public function __construct(
        public readonly int                 $programItemId,
        public readonly int                 $programId,
        public readonly ?int                $eventSessionId,
        public readonly ?int                $historyTourId,
        public readonly ?int                $passTypeId,
        public readonly ?\DateTimeImmutable $passValidDate,
        public readonly int                 $quantity,
        public readonly ?string             $donationAmount,
    )
    {
    }

    /**
     * Creates a ProgramItem instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            programItemId: (int)$row['ProgramItemId'],
            programId: (int)$row['ProgramId'],
            eventSessionId: isset($row['EventSessionId']) ? (int)$row['EventSessionId'] : null,
            historyTourId: isset($row['HistoryTourId']) ? (int)$row['HistoryTourId'] : null,
            passTypeId: isset($row['PassTypeId']) ? (int)$row['PassTypeId'] : null,
            passValidDate: isset($row['PassValidDate']) ? new \DateTimeImmutable($row['PassValidDate']) : null,
            quantity: (int)$row['Quantity'],
            donationAmount: $row['DonationAmount'] ?? null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'ProgramItemId' => $this->programItemId,
            'ProgramId' => $this->programId,
            'EventSessionId' => $this->eventSessionId,
            'HistoryTourId' => $this->historyTourId,
            'PassTypeId' => $this->passTypeId,
            'PassValidDate' => $this->passValidDate?->format('Y-m-d'),
            'Quantity' => $this->quantity,
            'DonationAmount' => $this->donationAmount,
        ];
    }
}
