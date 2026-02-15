<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS events list page.
 *
 * Contains all data needed to render the events list with weekly schedule.
 */
class CmsEventsListViewModel
{
    /**
     * @param array<array{EventId: int, Title: string, EventTypeName: string, EventTypeSlug: string, VenueName: ?string, SessionCount: int, IsActive: bool}> $events
     * @param array<array{EventTypeId: int, Name: string, Slug: string}> $eventTypes
     * @param array<array{VenueId: int, Name: string, AddressLine: string}> $venues
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
