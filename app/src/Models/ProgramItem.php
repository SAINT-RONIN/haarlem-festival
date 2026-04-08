<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ProgramItem table.
 *
 * Links a program to a specific event session with a chosen quantity and optional donation amount.
 */
final readonly class ProgramItem
{
    /*
     * Purpose: Stores individual items in a user's program/cart
     * (tickets, passes, tours) before checkout.
     */

    public function __construct(
        public int                 $programItemId,
        public int                 $programId,
        public ?int                $eventSessionId,
        public ?int                $historyTourId,
        public ?int                $passTypeId,
        public ?\DateTimeImmutable $passValidDate,
        public int                 $quantity,
        public ?int                $priceTierId,
        public ?string             $donationAmount,
        public ?int                $reservationId = null,
    ) {}

    /**
     * Creates a ProgramItem instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            programItemId: (int) $row['ProgramItemId'],
            programId: (int) $row['ProgramId'],
            eventSessionId: isset($row['EventSessionId']) ? (int) $row['EventSessionId'] : null,
            historyTourId: isset($row['HistoryTourId']) ? (int) $row['HistoryTourId'] : null,
            passTypeId: isset($row['PassTypeId']) ? (int) $row['PassTypeId'] : null,
            passValidDate: isset($row['PassValidDate']) ? new \DateTimeImmutable($row['PassValidDate']) : null,
            quantity: (int) $row['Quantity'],
            priceTierId: isset($row['PriceTierId']) ? (int) $row['PriceTierId'] : null,
            donationAmount: $row['DonationAmount'] ?? null,
            reservationId: isset($row['ReservationId']) ? (int) $row['ReservationId'] : null,
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
            'ReservationId' => $this->reservationId,
        ];
    }
}
