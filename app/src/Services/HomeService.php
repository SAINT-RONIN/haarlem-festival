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

    /**
     * Maps event type slugs to Tailwind CSS classes for badges.
     */
    private const BADGE_COLORS = [
        'jazz' => 'bg-sky-600/80',
        'dance' => 'bg-orange-800/80',
        'history' => 'bg-amber-400/80',
        'restaurant' => 'bg-lime-700/80',
        'storytelling' => 'bg-violet-800/80',
    ];

    /**
     * Maps event type slugs to schedule card border colors.
     */
    private const SCHEDULE_COLORS = [
        'jazz' => 'bg-sky-600',
        'dance' => 'bg-sky-800',
        'history' => 'bg-orange-800',
        'restaurant' => 'bg-lime-700',
        'storytelling' => 'bg-violet-800',
    ];

    /**
     * Order of event types for homepage showcase display.
     */
    private const EVENT_TYPE_ORDER = ['jazz', 'dance', 'history', 'restaurant', 'storytelling'];

    /**
     * Event type showcase content for the homepage.
     */
    private const EVENT_TYPE_CONTENT = [
        'jazz' => [
            'title' => 'Jazz',
            'description' => 'Experience world-class jazz performances at Haarlem\'s most iconic venues, where every note feels close, alive, and full of character. From intimate late night sessions to grand concerts featuring internationally acclaimed artists, the festival offers something for every jazz lover, whether you are discovering the genre or already know what you like. Expect smooth melodies, bold improvisation, and those rare moments when the whole room holds its breath together.',
            'darkBg' => true,
        ],
        'dance' => [
            'title' => 'Dance',
            'description' => 'Feel the rhythm at our dance events with electronic beats, live DJs, and vibrant club nights across multiple venues throughout the city. From pulsing dance floors to immersive light and sound, each night is designed to build energy, spark connection, and keep you moving. Come with friends or show up solo, you will be surrounded by people who are there for the same reason: great music and a real night out. Lose yourself in the vibe, discover new DJs, and celebrate Haarlem until the early hours.',
            'darkBg' => false,
        ],
        'history' => [
            'title' => 'History',
            'description' => 'Discover Haarlem\'s rich heritage through guided walking tours, museum visits, and historical storytelling that brings the past to life. Walk in the footsteps of centuries as expert guides reveal hidden details, iconic landmarks, and the surprising moments that shaped this remarkable city. Whether you are new to Haarlem or think you already know it, you will see familiar streets with fresh eyes and leave with stories worth sharing.',
            'darkBg' => true,
        ],
        'restaurant' => [
            'title' => 'Restaurants',
            'description' => 'Savor exceptional cuisine at Haarlem\'s finest restaurants and discover why the city is becoming a true food destination. Enjoy exclusive dining experiences, special chef collaborations, and hands-on culinary workshops designed for both curious beginners and passionate food lovers. From locally sourced ingredients to bold international inspiration, every dish is crafted to surprise and delight. Bring your appetite, treat yourself, and join fellow food lovers for unforgettable flavors, great company, and a festival experience you will be talking about long after the last bite.',
            'darkBg' => false,
        ],
        'storytelling' => [
            'title' => 'Storytelling',
            'description' => 'Immerse yourself in captivating narratives from local storytellers as they bring Haarlem\'s culture, legends, and present day life to vivid reality. Step into intimate festival settings where the art of oral tradition comes alive, and every story feels personal, surprising, and unforgettable. Come with friends or meet new people as you listen, laugh, and discover Haarlem through voices that know it best.',
            'darkBg' => true,
        ],
    ];

    public function __construct()
    {
        $this->eventTypeRepository = new EventTypeRepository();
        $this->venueRepository = new VenueRepository();
        $this->restaurantRepository = new RestaurantRepository();
        $this->eventSessionRepository = new EventSessionRepository();
    }

    /**
     * Builds the homepage view model with all required data.
     */
    public function getHomePageData(): HomePageViewModel
    {
        return new HomePageViewModel(
            eventTypes: $this->buildEventTypes(),
            locations: $this->buildLocations(),
            scheduleDays: $this->buildScheduleDays(),
        );
    }

    /**
     * Builds event type showcase data with precomputed styles.
     */
    private function buildEventTypes(): array
    {
        $types = $this->eventTypeRepository->findAll();
        $typesBySlug = [];

        // Index types by slug for easy lookup
        foreach ($types as $type) {
            $typesBySlug[$type['Slug']] = $type;
        }

        $result = [];

        // Build in the defined display order
        foreach (self::EVENT_TYPE_ORDER as $slug) {
            if (!isset($typesBySlug[$slug])) {
                continue;
            }

            $content = self::EVENT_TYPE_CONTENT[$slug] ?? null;

            if ($content === null) {
                continue;
            }

            $result[] = [
                'slug' => $slug,
                'title' => $content['title'],
                'description' => $content['description'],
                'darkBg' => $content['darkBg'],
                'badgeClass' => self::BADGE_COLORS[$slug] ?? 'bg-gray-500',
            ];
        }

        return $result;
    }

    /**
     * Builds locations list from venues and restaurants.
     */
    private function buildLocations(): array
    {
        $locations = [];

        // Add venues with their associated event types
        $venues = $this->venueRepository->findAllActive();
        foreach ($venues as $venue) {
            $category = $this->determineVenueCategory($venue['Name']);
            $locations[] = [
                'name' => $venue['Name'],
                'address' => $venue['AddressLine'],
                'category' => $category,
                'badgeClass' => self::BADGE_COLORS[$category] ?? 'bg-gray-500',
            ];
        }

        // Add restaurants
        $restaurants = $this->restaurantRepository->findAllActive();
        foreach ($restaurants as $restaurant) {
            $locations[] = [
                'name' => $restaurant['Name'],
                'address' => $restaurant['AddressLine'],
                'category' => 'restaurant',
                'badgeClass' => self::BADGE_COLORS['restaurant'],
            ];
        }

        return $locations;
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

        // Group sessions by date
        $grouped = [];
        foreach ($sessions as $session) {
            $date = (new \DateTime($session['StartDateTime']))->format('Y-m-d');

            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $session;
        }

        // If no sessions, return placeholder days
        if (empty($grouped)) {
            return $this->buildPlaceholderDays();
        }

        // Take only first 4 unique days for display
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
     */
    private function getEventSummaryTitle(string $slug, array $sessions): string
    {
        return match ($slug) {
            'jazz' => 'Haarlem Jazz @ Patronaat',
            'dance' => 'DANCE! (Back2Back & Club Sessions)',
            'history' => 'A Stroll through History (Tour)',
            'restaurant' => 'Yummy! Dinner Sessions',
            'storytelling' => 'Stories in Haarlem',
            default => $sessions[0]['EventTitle'] ?? 'Event',
        };
    }

    /**
     * Builds placeholder days when no sessions exist.
     */
    private function buildPlaceholderDays(): array
    {
        $placeholderDates = ['2026-07-25', '2026-07-26', '2026-07-27', '2026-07-28'];
        $dayNames = ['Saturday', 'Sunday', 'Monday', 'Tuesday'];

        $result = [];
        foreach ($placeholderDates as $i => $date) {
            $dateObj = new \DateTime($date);
            $result[] = [
                'date' => $date,
                'dayName' => $dayNames[$i],
                'dayNumber' => $dateObj->format('j'),
                'monthShort' => 'JUL',
                'eventCount' => 0,
                'sessions' => [],
            ];
        }

        return $result;
    }
}

