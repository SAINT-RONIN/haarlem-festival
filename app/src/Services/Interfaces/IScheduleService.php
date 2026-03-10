<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IScheduleService
{
    /**
     * Returns raw schedule data for any event type.
     * The ViewModel layer maps this to ScheduleSectionViewModel.
     *
     * @return array{cmsContent: array, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array}
     */
    public function getScheduleData(string $pageSlug, int $eventTypeId, int $maxDays = 4): array;
}
