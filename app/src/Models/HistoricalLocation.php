<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the HistoricalLocation table.
 *
 * HistoricalLocations are the venues in historical tour route.
 */
final readonly class HistoricalLocation
{
    public function __construct(
        public int                $historicalLocationId,
        public string             $name,
        public string             $description,
        public string             $badgeColor,
        public \DateTimeImmutable $createdAtUtc,
        public bool               $isActive,
    ) {}

    /**
     * Creates a HistoricalLocation instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            historicalLocationId: (int) $row['HistoricalLocationId'],
            name: (string) $row['Name'],
            description: (string) $row['Description'],
            badgeColor: (string) $row['BadgeColor'],
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
            'HistoricalLocationId' => $this->historicalLocationId,
            'Name' => $this->name,
            'Description' => $this->description,
            'BadgeColor' => $this->badgeColor,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'IsActive' => $this->isActive,
        ];
    }
}
