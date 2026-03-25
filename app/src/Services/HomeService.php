<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Filters\EventSessionFilter;
use App\Models\EventType;
use App\DTOs\Pages\HomeEventTypeData;
use App\DTOs\Pages\HomeLocationData;
use App\DTOs\Pages\HomePageData;
use App\DTOs\Pages\HomeScheduleDayData;
use App\DTOs\Pages\HomeScheduleSessionData;
use App\Models\Restaurant;
use App\Models\Venue;
use App\DTOs\Filters\VenueFilter;
use App\Repositories\GlobalContentRepository;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Exceptions\PageLoadException;
use App\Services\Interfaces\IHomeService;
use App\Constants\HomeUiConfig;

/**
 * Assembles all data needed to render the festival homepage into a single HomePageData object.
 *
 * Combines five data sources: CMS key-value content, hero/global-UI sections,
 * event-type showcase cards (ordered by HomeUiConfig), venue + restaurant map
 * locations, and a schedule preview limited to the next 4 days of active sessions.
 * Returns raw domain data only -- view model mapping happens in HomeMapper.
 */
class HomeService implements IHomeService
{
    public function __construct(
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IVenueRepository $venueRepository,
        private readonly IRestaurantRepository $restaurantRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly ICmsContentRepository $cmsService,
        private readonly GlobalContentRepository $globalContentRepo,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    /**
     * Returns all raw data needed to render the home page.
     *
     * Combines CMS content, hero/global-UI sections, event-type showcase
     * cards, venue/restaurant map locations, and a schedule preview
     * (up to 4 days) into a single HomePageData object.
     */
    public function getHomePageData(): HomePageData
    {
        try {
            return $this->assembleHomePageData();
        } catch (\Throwable $error) {
            throw new PageLoadException('Failed to load the home page.', 0, $error);
        }
    }

    /** Fetches and combines all data sources for the home page. */
    private function assembleHomePageData(): HomePageData
    {
        // Load all CMS key-value content for the home page (used by event-type cards)
        $cmsContent = $this->cmsService->getHomePageContent();

        return new HomePageData(
            cmsContent: $cmsContent,
            heroContent: $this->globalContentRepo->findHeroContent('home'),
            globalUiContent: $this->globalUiLoader->load(),
            eventTypes: $this->buildEventTypes($cmsContent),
            locations: $this->buildLocations(),
            scheduleDays: $this->buildScheduleDays(),
        );
    }

    /**
     * Builds event type showcase data with precomputed styles.
     *
     * @param array<string, array<string, ?string>> $cmsContent
     * @return HomeEventTypeData[]
     */
    private function buildEventTypes(array $cmsContent): array
    {
        $types = $this->eventTypeRepository->findEventTypes();
        $typesBySlug = $this->indexTypesBySlug($types);

        $result = [];
        foreach (HomeUiConfig::EVENT_TYPE_ORDER as $slug) {
            $eventType = $this->buildSingleEventType($slug, $typesBySlug, $cmsContent);
            if ($eventType !== null) {
                $result[] = $eventType;
            }
        }

        return $result;
    }

    /**
     * Indexes event types by slug for quick lookup.
     *
     * @param EventType[] $types
     * @return array<string, EventType>
     */
    private function indexTypesBySlug(array $types): array
    {
        $typesBySlug = [];
        foreach ($types as $type) {
            $typesBySlug[$type->slug] = $type;
        }
        return $typesBySlug;
    }

    /**
     * Builds data for a single event type, or returns null if not available.
     *
     * @param array<string, EventType> $typesBySlug
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private function buildSingleEventType(string $slug, array $typesBySlug, array $cmsContent): ?HomeEventTypeData
    {
        if (!isset($typesBySlug[$slug])) {
            return null;
        }

        $sectionKey = HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['sectionKey'] ?? null;
        if (!$sectionKey || !isset($cmsContent[$sectionKey])) {
            return null;
        }

        $section = $cmsContent[$sectionKey];

        return new HomeEventTypeData(
            slug: $slug,
            title: (string)($section[$slug . '_title'] ?? ucfirst($slug)),
            description: (string)($section[$slug . '_description'] ?? ''),
            button: (string)($section[$slug . '_button'] ?? 'Explore Events'),
            image: $section[$slug . '_image'] ?? null,
            darkBg: HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['darkBg'] ?? false,
        );
    }

    /**
     * Builds locations list from venues and restaurants.
     *
     * @return HomeLocationData[]
     */
    private function buildLocations(): array
    {
        $locations = [];

        foreach ($this->venueRepository->findVenues(new VenueFilter(isActive: true)) as $venue) {
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
    private function buildVenueLocation(Venue $venue): HomeLocationData
    {
        $category = $this->determineVenueCategory($venue->name);

        return new HomeLocationData(
            name: $venue->name,
            address: $venue->addressLine,
            category: $category,
            lat: null,
            lng: null,
        );
    }

    /**
     * Builds location data for a single restaurant.
     */
    private function buildRestaurantLocation(Restaurant $restaurant): HomeLocationData
    {
        return new HomeLocationData(
            name: $restaurant->name,
            address: $restaurant->addressLine,
            category: 'restaurant',
            lat: null,
            lng: null,
        );
    }

    /**
     * Maps a venue to an event-type category by matching known keywords in
     * the venue name. Falls back to 'history' for unrecognised venues
     * (walking-tour locations).
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
     *
     * @return HomeScheduleDayData[]
     */
    private function buildScheduleDays(): array
    {
        $sessions = $this->eventSessionRepository->findSessions(new EventSessionFilter(
            isActive: true,
            eventIsActive: true,
            includeCancelled: false,
            orderBy: 'es.StartDateTime ASC',
        ))->sessions;
        $grouped = $this->groupSessionsByDate($sessions);

        if (empty($grouped)) {
            return $this->buildPlaceholderDays();
        }

        return $this->buildScheduleDaysFromGrouped($grouped);
    }

    /**
     * Groups sessions by date string (Y-m-d).
     *
     * @param \App\DTOs\Schedule\SessionWithEvent[] $sessions
     * @return array<string, \App\DTOs\Schedule\SessionWithEvent[]>
     */
    private function groupSessionsByDate(array $sessions): array
    {
        $grouped = [];
        foreach ($sessions as $session) {
            $date = $session->startDateTime->format('Y-m-d');
            $grouped[$date][] = $session;
        }
        return $grouped;
    }

    /**
     * Builds schedule days from grouped session data.
     *
     * @param array<string, \App\DTOs\Schedule\SessionWithEvent[]> $grouped
     * @return HomeScheduleDayData[]
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
     *
     * @param \App\DTOs\Schedule\SessionWithEvent[] $sessions
     */
    private function buildDayData(string $date, array $sessions): HomeScheduleDayData
    {
        $byType = $this->groupSessionsByType($sessions);

        return new HomeScheduleDayData(
            date: $date,
            eventCount: count($byType),
            sessions: $this->collectSessionsForDisplay($byType),
        );
    }

    /**
     * Groups sessions by event type slug.
     *
     * @param \App\DTOs\Schedule\SessionWithEvent[] $sessions
     * @return array<string, array{typeName: string, typeSlug: string, sessions: \App\DTOs\Schedule\SessionWithEvent[]}>
     */
    private function groupSessionsByType(array $sessions): array
    {
        $byType = [];
        foreach ($sessions as $session) {
            $slug = $session->eventTypeSlug;
            if (!isset($byType[$slug])) {
                $byType[$slug] = [
                    'typeName' => $session->eventTypeName,
                    'typeSlug' => $slug,
                    'sessions' => [],
                ];
            }
            $byType[$slug]['sessions'][] = $session;
        }

        return $byType;
    }

    /**
     * Collects session data grouped by type for mapper formatting.
     *
     * @param array<string, array{typeName: string, typeSlug: string, sessions: \App\DTOs\Schedule\SessionWithEvent[]}> $byType
     * @return HomeScheduleSessionData[]
     */
    private function collectSessionsForDisplay(array $byType): array
    {
        $result = [];

        foreach ($byType as $slug => $typeData) {
            $sessions = $typeData['sessions'];
            $starts   = array_map(fn ($s) => $s->startDateTime->getTimestamp(), $sessions);
            $ends     = array_map(fn ($s) => $s->endDateTime ? $s->endDateTime->getTimestamp() : $s->startDateTime->getTimestamp(), $sessions);

            $result[] = new HomeScheduleSessionData(
                earliestStart: min($starts),
                latestEnd: max($ends),
                eventTypeSlug: $slug,
                firstEventTitle: (string)($sessions[0]->eventTitle ?? ''),
                typeName: (string)($typeData['typeName']),
            );
        }

        return $result;
    }

    /**
     * Builds placeholder days when no sessions exist.
     *
     * @return HomeScheduleDayData[]
     */
    private function buildPlaceholderDays(): array
    {
        $result = [];
        foreach (HomeUiConfig::PLACEHOLDER_DATES as $date) {
            $result[] = $this->buildSinglePlaceholderDay($date);
        }
        return $result;
    }

    /**
     * Builds data for a single placeholder day.
     */
    private function buildSinglePlaceholderDay(string $date): HomeScheduleDayData
    {
        return new HomeScheduleDayData(date: $date, eventCount: 0, sessions: []);
    }
}
