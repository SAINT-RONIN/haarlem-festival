<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `PriceTier` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class PriceTier
{
    /*
     * Purpose: Defines pricing categories (Adult, Child, Family)
     * for ticket pricing configuration.
     */

    public function __construct(
        public int $priceTierId,
        public string $name,
    ) {
    }

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
