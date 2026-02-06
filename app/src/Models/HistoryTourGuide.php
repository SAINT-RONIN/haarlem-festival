<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `HistoryTourGuide` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class HistoryTourGuide
{
    /*
     * Purpose: Links guides to history tours (many-to-many relationship).
     */

    public function __construct(
        public int $historyTourId,
        public int $guideId,
    ) {
    }

    /**
     * Creates a HistoryTourGuide instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            historyTourId: (int) $row['HistoryTourId'],
            guideId: (int) $row['GuideId'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'HistoryTourId' => $this->historyTourId,
            'GuideId' => $this->guideId,
        ];
    }
}

