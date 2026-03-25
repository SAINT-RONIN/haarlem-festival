<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Filters\ScheduleFilterParams;

/**
 * Defines the contract for building schedule page data for any event type.
 */
interface IScheduleService
{
    /**
     * Returns raw schedule data for any event type.
     * The ViewModel layer maps this to ScheduleSectionViewModel.
     *
     * @return array{cmsContent: array, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array, activeFilters: ?ScheduleFilterParams, availableDays: array, filterGroupTypes: string[], priceTypeOptions: string[]}
     */
    public function getScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays = 4,
        ?int $eventId = null,
        ?string $ctaTextOverride = null,
        ?ScheduleFilterParams $filterParams = null,
    ): array;
}
