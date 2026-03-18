<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * Generic ViewModel for an event card in the schedule, works for all event types.
 * Contains all fields needed by any card type (Storytelling, Jazz, History, Dance).
 */
final readonly class ScheduleEventCardViewModel
{
    /**
     * @param int $eventSessionId Session ID for linking
     * @param int $eventId Event ID
     * @param string $eventTypeSlug Event type slug for card layout selection
     * @param int $eventTypeId Event type ID
     * @param string $title Event title from DB
     * @param string $priceDisplay Formatted price or pay-what-you-like text
     * @param bool $isPayWhatYouLike Whether this is a pay-what-you-like event
     * @param string $ctaLabel CTA button text (from session or CMS default)
     * @param string $ctaUrl CTA link URL
     * @param string $locationName Venue name from DB
     * @param string $hallName Hall/stage name (for jazz: Main Hall, Outdoor Stage, etc.)
     * @param string $dateDisplay Formatted date string
     * @param string $isoDate ISO date for <time> element
     * @param string $timeDisplay Formatted time range
     * @param string $startTimeIso ISO time for <time> element
     * @param string $endTimeIso ISO time for <time> element
     * @param array<string> $labels Array of label texts
     * @param string $locationDisplay Pre-formatted location display string
     * @param int|null $capacityTotal Total venue capacity (for Jazz: displayed as "X seats")
     * @param int|null $seatsAvailable Available seats (Jazz specific)
     * @param int|null $minAge Minimum age restriction for this session
     * @param int|null $maxAge Maximum age restriction for this session
     * @param string|null $ageLabel Pre-formatted age restriction label (e.g., "Age 12+")
     * @param string|null $historyTicketLabel History ticket label text (History specific)
     * @param string|null $artistName Artist name (Jazz specific)
     * @param string|null $artistImageUrl Artist image URL (Jazz specific)
     * @param string|null $historyVenue Start venue name for history tours (History specific)
     * @param string|null $groupTicketInfo Group ticket information text (History specific)
     */
    public function __construct(
        public int     $eventSessionId,
        public int     $eventId,
        public string  $eventTypeSlug,
        public int     $eventTypeId,
        public string  $title,
        public string  $priceDisplay,
        public bool    $isPayWhatYouLike,
        public string  $ctaLabel,
        public string  $ctaUrl,
        public string  $locationName,
        public string  $hallName,
        public string  $dateDisplay,
        public string  $isoDate,
        public string  $timeDisplay,
        public string  $startTimeIso,
        public string  $endTimeIso,
        public array   $labels,
        public string  $locationDisplay = '',
        public ?int    $capacityTotal = null,
        public ?int    $seatsAvailable = null,
        public ?int    $minAge = null,
        public ?int    $maxAge = null,
        public ?string $ageLabel = null,
        public ?string $historyTicketLabel = null,
        public ?string $artistName = null,
        public ?string $artistImageUrl = null,
        public ?string $historyVenue = null,
        public ?string $groupTicketInfo = null,
        public string  $confirmText = '',
        public string  $addingText = '',
        public string  $successText = '',
    ) {
    }
}
