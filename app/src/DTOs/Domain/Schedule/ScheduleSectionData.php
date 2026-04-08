<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

use App\Constants\ScheduleConstants;
use App\DTOs\Domain\Filters\ScheduleFilterParams;
use App\DTOs\Cms\ScheduleSectionContent;

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
        public array $filterGroupTypes = [ScheduleConstants::FILTER_DAY],
        public array $priceTypeOptions = [ScheduleConstants::PRICE_TYPE_PAY_WHAT_YOU_LIKE, ScheduleConstants::PRICE_TYPE_FIXED],
    ) {
    }
}
