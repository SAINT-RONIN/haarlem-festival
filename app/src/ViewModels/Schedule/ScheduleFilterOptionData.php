<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * Represents a single filter option button (e.g., "Thursday", "Morning", "All") in a filter group.
 */
final readonly class ScheduleFilterOptionData
{
    public function __construct(
        public string $label,
        public string $value,
        public bool $isDefault = false,
        public bool $isActive = false,
    ) {
    }
}
