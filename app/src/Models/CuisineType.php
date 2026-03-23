<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `CuisineType` table.
 */
final readonly class CuisineType
{
    public function __construct(
        public int    $cuisineTypeId,
        public string $name,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            cuisineTypeId: (int)$row['CuisineTypeId'],
            name:          (string)$row['Name'],
        );
    }
}
