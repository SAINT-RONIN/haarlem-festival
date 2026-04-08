<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the CuisineType table.
 *
 * Cuisine categories linked to restaurant events for filtering.
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
            cuisineTypeId: (int) $row['CuisineTypeId'],
            name: (string) $row['Name'],
        );
    }
}
