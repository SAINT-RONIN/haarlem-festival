<?php

declare(strict_types=1);

namespace App\DTOs\Schedule;

use App\DTOs\Filters\ScheduleFilterParams;
use App\Content\ScheduleSectionContent;

/**
 * Typed schedule payload assembled by ScheduleService for the schedule mappers.
 *
 * Keeps the service-to-mapper boundary explicit without introducing a deeper
 * nested DTO graph for the existing day/event card arrays.
 */
final readonly class ScheduleSectionData
{
    /**
     * @param array<array{dayName: string, isoDate: string, events: array<array<string, mixed>>, isEmpty: bool}> $days
     * @param ScheduleDayData[] $availableDays
     * @param string[] $filterGroupTypes
     * @param string[] $priceTypeOptions
     */
    public function __construct(
        public ScheduleSectionContent $cmsContent,
        public string $pageSlug,
        public string $eventTypeSlug,
        public int $eventTypeId,
        public array $days,
        public ?ScheduleFilterParams $activeFilters = null,
        public array $availableDays = [],
        public array $filterGroupTypes = ['day'],
        public array $priceTypeOptions = ['pay-what-you-like', 'fixed'],
    ) {
    }
}
