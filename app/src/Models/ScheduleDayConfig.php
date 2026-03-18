<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ScheduleDayConfig
{
    public function __construct(
        public int $scheduleDayConfigId,
        public ?int $eventTypeId,
        public int $dayOfWeek,
        public bool $isVisible,
        public ?string $eventTypeName,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            scheduleDayConfigId: (int) $row['ScheduleDayConfigId'],
            eventTypeId: isset($row['EventTypeId']) && $row['EventTypeId'] !== null
                ? (int) $row['EventTypeId']
                : null,
            dayOfWeek: (int) $row['DayOfWeek'],
            isVisible: (bool) $row['IsVisible'],
            eventTypeName: $row['EventTypeName'] ?? null,
        );
    }
}
