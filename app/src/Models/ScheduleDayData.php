<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single day row returned by the groupByDay query or findDistinctDays in EventSessionRepository.
 *
 * The days query selects: DATE(es.StartDateTime) AS Date, DAYNAME(es.StartDateTime) AS dayName
 */
final readonly class ScheduleDayData
{
    public function __construct(
        public string $date,
        public string $dayOfWeek = '',
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            date: (string)($row['Date'] ?? $row['date'] ?? throw new \InvalidArgumentException('Missing required field: date')),
            dayOfWeek: (string)($row['dayName'] ?? ''),
        );
    }
}
