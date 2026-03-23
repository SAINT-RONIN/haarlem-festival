<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ScheduleDay` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class ScheduleDay
{
    /*
     * Purpose: Defines which days each event type runs during
     * the festival for schedule display.
     */

    public function __construct(
        public readonly int                 $scheduleDayId,
        public readonly int                 $eventTypeId,
        public readonly \DateTimeImmutable  $date,
        public readonly bool                $isDeleted,
        public readonly ?\DateTimeImmutable $deletedAtUtc,
    ) {
    }

    /**
     * Creates a ScheduleDay instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            scheduleDayId: (int)$row['ScheduleDayId'],
            eventTypeId: (int)$row['EventTypeId'],
            date: new \DateTimeImmutable($row['Date']),
            isDeleted: (bool)$row['IsDeleted'],
            deletedAtUtc: isset($row['DeletedAtUtc']) ? new \DateTimeImmutable($row['DeletedAtUtc']) : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'ScheduleDayId' => $this->scheduleDayId,
            'EventTypeId' => $this->eventTypeId,
            'Date' => $this->date->format('Y-m-d'),
            'IsDeleted' => $this->isDeleted,
            'DeletedAtUtc' => $this->deletedAtUtc?->format('Y-m-d H:i:s'),
        ];
    }
}
