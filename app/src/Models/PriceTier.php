<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the PriceTier table.
 *
 * Defines named pricing categories (Adult, Child U12, Family, Reservation Fee) that
 * sessions can have prices for.
 */
final readonly class PriceTier
{
    /*
     * Purpose: Defines pricing categories (Adult, Child, Family)
     * for ticket pricing configuration.
     */

    public function __construct(
        public int    $priceTierId,
        public string $name,
    ) {}

    /**
     * Creates a PriceTier instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            priceTierId: (int) $row['PriceTierId'],
            name: (string) $row['Name'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'PriceTierId' => $this->priceTierId,
            'Name' => $this->name,
        ];
    }
}
