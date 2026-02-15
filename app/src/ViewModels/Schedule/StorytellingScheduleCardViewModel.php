<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * ViewModel for an individual event card in the storytelling schedule.
 */
final readonly class StorytellingScheduleCardViewModel
{
    /**
     * @param int $eventSessionId Session ID for linking
     * @param string $title Event title from DB
     * @param string $priceDisplay Formatted price or pay-what-you-like text
     * @param bool $isPayWhatYouLike Whether this is a pay-what-you-like event
     * @param string $ctaLabel CTA button text (from session or CMS default)
     * @param string $ctaUrl CTA link URL
     * @param string $locationName Venue name from DB
     * @param string $hallName Hall/stage name (for jazz: Main Hall, Outdoor Stage, etc.)
     * @param int $seatsAvailable Number of available seats (for jazz events)
     * @param string $eventTypeSlug Event type slug (jazz, storytelling, history, etc.)
     * @param string $dateDisplay Formatted date string
     * @param string $isoDate ISO date for <time> element
     * @param string $timeDisplay Formatted time range
     * @param string $startTimeIso ISO time for <time> element
     * @param string $endTimeIso ISO time for <time> element
     * @param array<string> $labels Array of label texts
     */
    public function __construct(
        public int    $eventSessionId,
        public string $title,
        public string $priceDisplay,
        public bool   $isPayWhatYouLike,
        public string $ctaLabel,
        public string $ctaUrl,
        public string $locationName,
        public string $hallName,
        public int    $seatsAvailable,
        public string $eventTypeSlug,
        public string $dateDisplay,
        public string $isoDate,
        public string $timeDisplay,
        public string $startTimeIso,
        public string $endTimeIso,
        public array  $labels,
    ) {
    }

    /**
     * Gets the full location display string.
     * For Jazz: "Venue • Hall • X seats"
     * For others: Just the venue name
     */
    public function getLocationDisplay(): string
    {
        if ($this->eventTypeSlug === 'jazz' && !empty($this->hallName)) {
            $parts = [$this->locationName];
            if (!empty($this->hallName)) {
                $parts[] = $this->hallName;
            }
            if ($this->seatsAvailable > 0) {
                $parts[] = $this->seatsAvailable . ' seats';
            }
            return implode(' • ', $parts);
        }

        return $this->locationName;
    }
}
