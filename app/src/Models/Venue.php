<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Venue` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Venue
{
    /*
     * Purpose: Stores venue/location information where festival
     * events take place.
     */

    public function __construct(
        public readonly int                $venueId,
        public readonly string             $name,
        public readonly string             $addressLine,
        public readonly string             $city,
        public readonly \DateTimeImmutable $createdAtUtc,
        public readonly bool               $isActive,
    ) {
    }

    /**
     * Creates a Venue instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            venueId: (int)$row['VenueId'],
            name: (string)$row['Name'],
            addressLine: (string)$row['AddressLine'],
            city: (string)$row['City'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            isActive: (bool)$row['IsActive'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'VenueId' => $this->venueId,
            'Name' => $this->name,
            'AddressLine' => $this->addressLine,
            'City' => $this->city,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'IsActive' => $this->isActive,
        ];
    }
}
