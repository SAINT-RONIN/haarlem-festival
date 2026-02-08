<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\VenueRepository;
use App\Services\Interfaces\IHomeService;
use App\ViewModels\HomePageViewModel;

/**
 * Service for preparing homepage data.
 *
 * Assembles all data needed for the homepage view, including
 * event types, locations, and schedule information.
 */
class HomeService implements IHomeService
{
    private EventTypeRepository $eventTypeRepository;
    private VenueRepository $venueRepository;
    private RestaurantRepository $restaurantRepository;
    private EventSessionRepository $eventSessionRepository;
    private CmsService $cmsService;

    private const BADGE_COLORS = [
        'jazz' => 'bg-azure-blue-80',
        'dance' => 'bg-deep-crimson-80',
        'history' => 'bg-amber-gold-80',
        'restaurant' => 'bg-olive-green-80',
        'storytelling' => 'bg-deep-purple-80',
    ];

    private const SCHEDULE_COLORS = [
        'jazz' => 'bg-azure-blue',
        'dance' => 'bg-deep-crimson',
        'history' => 'bg-amber-gold',
        'restaurant' => 'bg-olive-green',
        'storytelling' => 'bg-deep-purple',
    ];

    private const EVENT_TYPE_ORDER = ['jazz', 'dance', 'history', 'restaurant', 'storytelling'];

    public function __construct()
    {
        $this->eventTypeRepository = new EventTypeRepository();
        $this->venueRepository = new VenueRepository();
        $this->restaurantRepository = new RestaurantRepository();
        $this->eventSessionRepository = new EventSessionRepository();
        $this->cmsService = new CmsService();
    }

    /**
     * Builds the homepage view model with all required data.
     */
    public function getHomePageData(): HomePageViewModel
    {
        $cmsContent = $this->cmsService->getHomePageContent();

        return new HomePageViewModel(
            heroData: $this->cmsService->buildHeroData('home', 'home'),
            globalUi: $this->cmsService->buildGlobalUiData(),
            eventTypes: $this->buildEventTypes($cmsContent),
            locations: $this->buildLocations(),
            scheduleDays: $this->buildScheduleDays(),
            cmsContent: $cmsContent,
        );
    }

    private const SECTION_MAP = [
        'jazz' => 'event_jazz',
        'dance' => 'event_dance',
        'history' => 'event_history',
        'restaurant' => 'event_restaurant',
        'storytelling' => 'event_storytelling',
    ];

    private const DARK_BG_MAP = [
        'jazz' => true,
        'dance' => false,
        'history' => true,
        'restaurant' => false,
        'storytelling' => true,
    ];

    /**
     * Builds event type showcase data with precomputed styles.
     */
    private function buildEventTypes(array $cmsContent): array
    {
        $types = $this->eventTypeRepository->findAll();
        $typesBySlug = $this->indexTypesBySlug($types);

        $result = [];
        foreach (self::EVENT_TYPE_ORDER as $slug) {
            $eventType = $this->buildSingleEventType($slug, $typesBySlug, $cmsContent);
            if ($eventType !== null) {
                $result[] = $eventType;
            }
        }

        return $result;
    }

    /**
     * Indexes event types by slug for quick lookup.
     */
    private function indexTypesBySlug(array $types): array
    {
        $typesBySlug = [];
        foreach ($types as $type) {
            $typesBySlug[$type['Slug']] = $type;
        }
        return $typesBySlug;
    }

    /**
     * Builds data for a single event type, or returns null if not available.
     */
    private function buildSingleEventType(string $slug, array $typesBySlug, array $cmsContent): ?array
    {
        if (!isset($typesBySlug[$slug])) {
            return null;
        }

        $sectionKey = self::SECTION_MAP[$slug] ?? null;
        if (!$sectionKey || !isset($cmsContent[$sectionKey])) {
            return null;
        }

        $section = $cmsContent[$sectionKey];

        return [
            'slug' => $slug,
            'title' => $section[$slug . '_title'] ?? ucfirst($slug),
            'description' => $section[$slug . '_description'] ?? '',
            'button' => $section[$slug . '_button'] ?? 'Explore Events',
            'darkBg' => self::DARK_BG_MAP[$slug] ?? false,
            'badgeClass' => self::BADGE_COLORS[$slug] ?? 'bg-gray-500',
        ];
    }


    /**
     * Builds locations list from venues and restaurants.
     */
    private function buildLocations(): array
    {
        $locations = [];

        foreach ($this->venueRepository->findAllActive() as $venue) {
            $locations[] = $this->buildVenueLocation($venue);
        }

        foreach ($this->restaurantRepository->findAllActive() as $restaurant) {
            $locations[] = $this->buildRestaurantLocation($restaurant);
        }

        return $locations;
    }

    /**
     * Builds location data for a single venue.
     */
    private function buildVenueLocation(array $venue): array
    {
        $category = $this->determineVenueCategory($venue['Name']);

        return [
            'name' => $venue['Name'],
            'address' => $venue['AddressLine'],
            'category' => $category,
            'badgeClass' => self::BADGE_COLORS[$category] ?? 'bg-gray-500',
        ];
    }

    /**
     * Builds location data for a single restaurant.
     */
    private function buildRestaurantLocation(array $restaurant): array
    {
        return [
            'name' => $restaurant['Name'],
            'address' => $restaurant['AddressLine'],
            'category' => 'restaurant',
            'badgeClass' => self::BADGE_COLORS['restaurant'],
        ];
    }


    /**
     * Determines venue category based on venue name/type.
     */
    private function determineVenueCategory(string $venueName): string
    {
        $name = strtolower($venueName);

        if (str_contains($name, 'patronaat')) {
            return 'jazz';
        }
        if (str_contains($name, 'club') || str_contains($name, 'lichtfabriek')
            || str_contains($name, 'slachthuis') || str_contains($name, 'jopenkerk')
            || str_contains($name, 'caprera') || str_contains($name, 'puncher')) {
            return 'dance';
        }
        if (str_contains($name, 'bavo') || str_contains($name, 'church')) {
            return 'history';
        }
        if (str_contains($name, 'verhalen') || str_contains($name, 'schuur')
            || str_contains($name, 'kweek') || str_contains($name, 'boom')
            || str_contains($name, 'theater')) {
            return 'storytelling';
        }

        return 'history'; // Default for walking tour locations
    }

    /**
     * Builds schedule days with grouped and formatted sessions.
     */
    private function buildScheduleDays(): array
    {
        $sessions = $this->eventSessionRepository->findUpcomingWithDetails();
        $grouped = $this->groupSessionsByDate($sessions);

        if (empty($grouped)) {
            return $this->buildPlaceholderDays();
        }

        return $this->buildScheduleDaysFromGrouped($grouped);
    }

    /**
     * Groups sessions by date string (Y-m-d).
     */
    private function groupSessionsByDate(array $sessions): array
    {
        $grouped = [];
        foreach ($sessions as $session) {
            $date = (new \DateTime($session['StartDateTime']))->format('Y-m-d');
            $grouped[$date][] = $session;
        }
        return $grouped;
    }

    /**
     * Builds schedule days from grouped session data.
     */
    private function buildScheduleDaysFromGrouped(array $grouped): array
    {
        $dates = array_keys($grouped);
        sort($dates);
        $dates = array_slice($dates, 0, 4);

        $result = [];
        foreach ($dates as $date) {
            $result[] = $this->buildDayData($date, $grouped[$date]);
        }

        return $result;
    }

    /**
     * Builds data for a single schedule day.
     */
    private function buildDayData(string $date, array $sessions): array
    {
        $dateObj = new \DateTime($date);

        // Group sessions by event type for summary display
        $byType = $this->groupSessionsByType($sessions);

        return [
            'date' => $date,
            'dayName' => $dateObj->format('l'),
            'dayNumber' => $dateObj->format('j'),
            'monthShort' => strtoupper($dateObj->format('M')),
            'eventCount' => count($byType),
            'sessions' => $this->formatSessionsForDisplay($byType),
        ];
    }

    /**
     * Groups sessions by event type slug.
     */
    private function groupSessionsByType(array $sessions): array
    {
        $byType = [];
        foreach ($sessions as $session) {
            $slug = $session['EventTypeSlug'];
            if (!isset($byType[$slug])) {
                $byType[$slug] = [
                    'typeName' => $session['EventTypeName'],
                    'typeSlug' => $slug,
                    'sessions' => [],
                ];
            }
            $byType[$slug]['sessions'][] = $session;
        }

        return $byType;
    }

    /**
     * Formats grouped sessions for display in schedule.
     */
    private function formatSessionsForDisplay(array $byType): array
    {
        $result = [];

        foreach ($byType as $slug => $typeData) {
            $sessions = $typeData['sessions'];
            $timeRange = $this->calculateTimeRange($sessions);

            $result[] = [
                'timeLabel' => $timeRange,
                'title' => $this->getEventSummaryTitle($slug, $sessions),
                'categoryLabel' => $typeData['typeName'],
                'borderClass' => self::SCHEDULE_COLORS[$slug] ?? 'bg-gray-500',
            ];
        }

        return $result;
    }

    /**
     * Calculates time range string from sessions.
     */
    private function calculateTimeRange(array $sessions): string
    {
        $starts = array_map(fn($s) => strtotime($s['StartDateTime']), $sessions);
        $ends = array_map(fn($s) => strtotime($s['EndDateTime']), $sessions);

        $minStart = min($starts);
        $maxEnd = max($ends);

        $startTime = date('H:i', $minStart);
        $endTime = date('H:i', $maxEnd);

        return "{$startTime} – {$endTime}";
    }

    /**
     * Gets summary title for event type sessions.
     *
     * TODO: These titles should be retrieved from the database (e.g., EventType.DisplayTitle)
     */
    private function getEventSummaryTitle(string $slug, array $sessions): string
    {
        // TODO: Hardcoded display titles - should be stored in database
        return match ($slug) {
            'jazz' => 'Haarlem Jazz @ Patronaat',
            'dance' => 'DANCE! (Back2Back & Club Sessions)',
            'history' => 'A Stroll through History (Tour)',
            'restaurant' => 'Yummy! Dinner Sessions',
            'storytelling' => 'Stories in Haarlem',
            default => $sessions[0]['EventTitle'] ?? 'Event',
        };
    }


    // TODO: Hardcoded placeholder dates - should be retrieved from database (e.g., Program.StartDate, Program.EndDate)
    private const PLACEHOLDER_DATES = ['2026-07-25', '2026-07-26', '2026-07-27', '2026-07-28'];
    // TODO: Hardcoded day names - should be computed from actual festival dates in database
    private const PLACEHOLDER_DAY_NAMES = ['Saturday', 'Sunday', 'Monday', 'Tuesday'];

    /**
     * Builds placeholder days when no sessions exist.
     */
    private function buildPlaceholderDays(): array
    {
        $result = [];
        foreach (self::PLACEHOLDER_DATES as $i => $date) {
            $result[] = $this->buildSinglePlaceholderDay($date, self::PLACEHOLDER_DAY_NAMES[$i]);
        }
        return $result;
    }

    /**
     * Builds data for a single placeholder day.
     */
    private function buildSinglePlaceholderDay(string $date, string $dayName): array
    {
        $dateObj = new \DateTime($date);

        return [
            'date' => $date,
            'dayName' => $dayName,
            'dayNumber' => $dateObj->format('j'),
            // TODO: Hardcoded month - should be derived from actual festival dates in database
            'monthShort' => 'JUL',
            'eventCount' => 0,
            'sessions' => [],
        ];
    }
}
