<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Guide` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Guide
{
    /*
     * Purpose: Holds tour guide information for history walking tours.
     */

    public function __construct(
        public readonly int    $guideId,
        public readonly string $name,
        public readonly bool   $isActive,
    )
    {
    }

    /**
     * Creates a Guide instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            guideId: (int)$row['GuideId'],
            name: (string)$row['Name'],
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
            'GuideId' => $this->guideId,
            'Name' => $this->name,
            'IsActive' => $this->isActive,
        ];
    }
}
