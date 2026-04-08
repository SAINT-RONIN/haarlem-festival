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
final readonly class CmsEventsListViewModel
{
    /**
     * @param CmsEventListItemViewModel[] $events Event items as ViewModels
     * @param EventType[] $eventTypes Event types as Models
     * @param Venue[] $venues Venues as Models
     * @param array<string, CmsEventSessionViewModel[]> $weeklySchedule Sessions grouped by day name
     * @param array<string, string> $typeColorMap Event type slug → Tailwind badge color classes
     * @param string $selectedType Current type filter
     * @param string $selectedDay Current day filter
     */
    public function __construct(
        public array   $events,
        public array   $eventTypes,
        public array   $venues,
        public array   $weeklySchedule,
        public array   $typeColorMap,
        public string  $selectedType,
        public string  $selectedDay,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}
