<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Schedule day configs grouped by scope.
 *
 * Global configs apply to all event types; type-specific configs override per event type.
 */
final readonly class GroupedScheduleDayConfigs
{
    /**
     * @param array<int, ScheduleDayConfig> $global   Day-of-week → config for global (all types)
     * @param array<int, array<int, ScheduleDayConfig>> $byType  EventTypeId → DayOfWeek → config
     */
    public function __construct(
        public array $global,
        public array $byType,
    ) {}
}
