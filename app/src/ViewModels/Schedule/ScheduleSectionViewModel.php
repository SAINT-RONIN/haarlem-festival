<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * Generic ViewModel for schedule section, works for all event types.
 */
final readonly class ScheduleSectionViewModel
{
    /**
     * @param string $sectionId HTML ID for the section (e.g., 'jazz-schedule')
     * @param string $title Section title from CMS
     * @param ?string $year Year display from CMS
     * @param string $eventTypeSlug Event type slug for card selection (storytelling, jazz, history, dance)
     * @param int $eventTypeId Event type ID
     * @param string $filtersButtonText Filters button text from CMS
     * @param bool $showFilters Whether to show filters button
     * @param string $additionalInfoTitle Info box title from CMS
     * @param string $additionalInfoBody Info box body HTML from CMS
     * @param bool $showAdditionalInfo Whether to show info box
     * @param ?string $eventCountLabel Label for event count from CMS (Stories, Performances, Tours, etc.)
     * @param ?int $eventCount Total number of events
     * @param bool $showEventCount Whether to show event count
     * @param string $ctaButtonText Default CTA button text from CMS
     * @param string $payWhatYouLikeText Pay-what-you-like display text from CMS
     * @param string $currencySymbol Currency symbol from CMS
     * @param string $noEventsText Text to show when no events on a day
     * @param array<ScheduleDayViewModel> $days Array of day columns
     */
    public function __construct(
        public string $sectionId,
        public string $title,
        public ?string $year,
        public string $eventTypeSlug,
        public int    $eventTypeId,
        public string $filtersButtonText,
        public bool   $showFilters,
        public string $additionalInfoTitle,
        public string $additionalInfoBody,
        public bool   $showAdditionalInfo,
        public ?string $eventCountLabel,
        public ?int    $eventCount,
        public bool   $showEventCount,
        public string $ctaButtonText,
        public string $payWhatYouLikeText,
        public string $currencySymbol,
        public string $noEventsText,
        public array  $days,
    ) {
    }
}
