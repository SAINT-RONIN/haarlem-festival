<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Read-only projection from EventSession aggregation.
 *
 * Provides the distinct days that have active sessions, used to build the schedule day tabs.
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
