<?php

declare(strict_types=1);

namespace App\DTOs\Filters;

/**
 * Parsed filter parameters from the schedule page URL (day, time range, price type,
 * language, venue). Read by BaseController, passed to ScheduleService.
 */
final readonly class ScheduleFilterParams
{
    public function __construct(
        public ?string $day = null,
        public ?string $timeRange = null,
        public ?string $priceType = null,
        public ?string $venue = null,
        public ?string $language = null,
        public ?int $age = null,
        public ?string $startTime = null,
    ) {
    }

    public function hasAnyFilter(): bool
    {
        return $this->day !== null
            || $this->timeRange !== null
            || $this->priceType !== null
            || $this->venue !== null
            || $this->language !== null
            || $this->age !== null
            || $this->startTime !== null;
    }
}
