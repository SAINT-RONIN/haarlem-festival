<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventSessionLabel` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class EventSessionLabel
{
    /*
     * Purpose: Stores display labels/tags for event sessions
     * (e.g., "Sold Out", "Last Tickets").
     */

    public function __construct(
        public int $eventSessionLabelId,
        public int $eventSessionId,
        public string $labelText,
    ) {
    }

    /**
     * Creates an EventSessionLabel instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionLabelId: (int) $row['EventSessionLabelId'],
            eventSessionId: (int) $row['EventSessionId'],
            labelText: (string) $row['LabelText'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'EventSessionLabelId' => $this->eventSessionLabelId,
            'EventSessionId' => $this->eventSessionId,
            'LabelText' => $this->labelText,
        ];
    }
}

