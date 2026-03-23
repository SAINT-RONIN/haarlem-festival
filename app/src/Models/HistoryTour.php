<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `HistoryTour` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class HistoryTour
{
    /*
     * Purpose: Defines a history walking tour variant for a session,
     * specifying language, guide count, and capacity.
     */

    public function __construct(
        public readonly int    $historyTourId,
        public readonly int    $eventSessionId,
        public readonly string $languageCode,
        public readonly int    $guideCount,
        public readonly int    $seatsPerTour,
    ) {
    }

    /**
     * Creates a HistoryTour instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            historyTourId: (int)$row['HistoryTourId'],
            eventSessionId: (int)$row['EventSessionId'],
            languageCode: (string)$row['LanguageCode'],
            guideCount: (int)$row['GuideCount'],
            seatsPerTour: (int)$row['SeatsPerTour'],
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
            'EventSessionId' => $this->eventSessionId,
            'LanguageCode' => $this->languageCode,
            'GuideCount' => $this->guideCount,
            'SeatsPerTour' => $this->seatsPerTour,
        ];
    }
}
