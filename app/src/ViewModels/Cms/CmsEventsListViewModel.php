<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\EventType;
use App\Models\Venue;

/**
 * ViewModel for the CMS events list page.
 *
 * Contains all data needed to render the events list with weekly schedule.
 */
class CmsEventsListViewModel
{
    /**
     * @param CmsEventListItemViewModel[] $events Event items as ViewModels
     * @param EventType[] $eventTypes Event types as Models
     * @param Venue[] $venues Venues as Models
     * @param array<string, CmsEventSessionViewModel[]> $weeklySchedule Sessions grouped by day name
     * @param string $selectedType Current type filter
     * @param string $selectedDay Current day filter
     */
    public function __construct(
        public readonly array   $events,
        public readonly array   $eventTypes,
        public readonly array   $venues,
        public readonly array   $weeklySchedule,
        public readonly string  $selectedType,
        public readonly string  $selectedDay,
        public readonly ?string $successMessage,
        public readonly ?string $errorMessage,
    ) {
    }
}
