<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

use App\Constants\ScheduleConstants;
use App\DTOs\Domain\Filters\ScheduleFilterParams;
use App\DTOs\Cms\ScheduleSectionContent;
use App\Models\EventSessionPrice;

/**
 * Typed schedule payload assembled by ScheduleService for the schedule mappers.
 *
 * Carries per-day session payloads plus the shared price map and display strings
 * so that ScheduleDayMapper can build event card arrays without calling back into the service.
 */
final readonly class ScheduleSectionData
{
    /**
     * @param ScheduleDayPayload[]            $days
     * @param ScheduleDayData[]               $availableDays
     * @param string[]                        $filterGroupTypes
     * @param string[]                        $priceTypeOptions
     * @param array<int, EventSessionPrice[]> $pricesMap
     */
    public function __construct(
        public ScheduleSectionContent  $cmsContent,
        public string                  $pageSlug,
        public string                  $eventTypeSlug,
        public int                     $eventTypeId,
        public array                   $days,
        public ScheduleDisplayStrings  $displayStrings,
        public array                   $pricesMap,
        public ?ScheduleFilterParams   $activeFilters = null,
        public array                   $availableDays = [],
        public array                   $filterGroupTypes = [ScheduleConstants::FILTER_DAY],
        public array                   $priceTypeOptions = [ScheduleConstants::PRICE_TYPE_PAY_WHAT_YOU_LIKE, ScheduleConstants::PRICE_TYPE_FIXED],
    ) {}
}
