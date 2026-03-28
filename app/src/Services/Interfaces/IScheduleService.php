<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Filters\ScheduleFilterParams;
use App\DTOs\Schedule\ScheduleSectionData;

/**
 * Defines the contract for building schedule page data for any event type.
 */
interface IScheduleService
{
    /**
     * Returns a typed schedule payload for any event type.
     */
    public function getScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays = 4,
        ?int $eventId = null,
        ?string $ctaTextOverride = null,
        ?ScheduleFilterParams $filterParams = null,
    ): ScheduleSectionData;
}
