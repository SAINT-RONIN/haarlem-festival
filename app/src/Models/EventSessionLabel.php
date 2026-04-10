<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the EventSessionLabel table.
 *
 * Labels are display badges shown on session cards (e.g., 'In Dutch', 'Age 16+', 'Sold Out').
 */
final readonly class EventSessionLabel
{
    /*
     * Purpose: Stores display labels/tags for event sessions
     * (e.g., "Sold Out", "Last Tickets").
     */

    public function __construct(
        public int    $eventSessionLabelId,
        public int    $eventSessionId,
        public string $labelText,
    ) {}

    /**
     * Creates an EventSessionLabel instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionLabelId: (int) ($row['EventSessionLabelId'] ?? throw new \InvalidArgumentException('Missing required field: EventSessionLabelId')),
            eventSessionId: (int) ($row['EventSessionId'] ?? throw new \InvalidArgumentException('Missing required field: EventSessionId')),
            labelText: (string) ($row['LabelText'] ?? throw new \InvalidArgumentException('Missing required field: LabelText')),
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
