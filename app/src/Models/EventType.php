<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the EventType table.
 *
 * Defines the festival categories (Jazz, Storytelling, History, Dance, Restaurant) that
 * determine which public page an event appears on.
 */
final readonly class EventType
{
    /*
     * Purpose: Categorizes events by type (Jazz, Dance, History, etc.)
     * for filtering and navigation.
     */

    public function __construct(
        public int    $eventTypeId,
        public string $name,
        public string $slug,
    ) {}

    /**
     * Creates an EventType instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventTypeId: (int) $row['EventTypeId'],
            name: (string) $row['Name'],
            slug: (string) $row['Slug'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'EventTypeId' => $this->eventTypeId,
            'Name' => $this->name,
            'Slug' => $this->slug,
        ];
    }
}
