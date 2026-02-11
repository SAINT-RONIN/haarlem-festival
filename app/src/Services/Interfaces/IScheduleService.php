<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Interface for Schedule building service.
 */
interface IScheduleService
{
    /**
     * Builds a schedule section ViewModel for any event type.
     *
     * @param string $pageSlug Page slug for CMS content
     * @param int $eventTypeId Event type ID to filter sessions
     * @param int $maxDays Maximum number of days to show
     * @return ScheduleSectionViewModel
     */
    public function buildScheduleSection(string $pageSlug, int $eventTypeId, int $maxDays = 4): ScheduleSectionViewModel;
}

