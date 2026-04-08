<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the Venue table.
 *
 * Venues are physical locations where events take place (e.g., Patronaat, Jopenkerk).
 */
final readonly class Venue
{
    /*
     * Purpose: Stores venue/location information where festival
     * events take place.
     */

    public function __construct(
        public int                $venueId,
        public string             $name,
        public string             $addressLine,
        public string             $city,
        public \DateTimeImmutable $createdAtUtc,
        public bool               $isActive,
    ) {}

    /**
     * Creates a Venue instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            venueId: (int) $row['VenueId'],
            name: (string) $row['Name'],
            addressLine: (string) $row['AddressLine'],
            city: (string) $row['City'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            isActive: (bool) $row['IsActive'],
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
