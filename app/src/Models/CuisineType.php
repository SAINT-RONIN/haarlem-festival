<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `CuisineType` table.
 */
class CuisineType
{
    public function __construct(
        public readonly int    $cuisineTypeId,
        public readonly string $name,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            cuisineTypeId: (int)$row['CuisineTypeId'],
            name:          (string)$row['Name'],
        );
    }
}
