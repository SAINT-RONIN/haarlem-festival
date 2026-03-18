<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single day row returned by the groupByDay query in EventSessionRepository.
 *
 * The days query selects: DATE(es.StartDateTime) AS Date
 */
final readonly class ScheduleDayData
{
    public function __construct(
        public string $date,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            date: (string)$row['Date'],
        );
    }
}
