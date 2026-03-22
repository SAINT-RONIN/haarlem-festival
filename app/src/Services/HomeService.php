<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EventSessionFilter;
use App\Models\EventType;
use App\Models\HomePageData;
use App\Models\Restaurant;
use App\Models\Venue;
use App\Models\VenueFilter;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Services\Interfaces\IHomeService;
use App\Utils\HomeUiConfig;

/**
 * Service for preparing homepage data.
 *
 * Returns plain arrays with raw data.
 * Mapping to ViewModels happens in HomeMapper.
 */
class HomeService implements IHomeService
{
    public function __construct(
        private IEventTypeRepository $eventTypeRepository,
        private IVenueRepository $venueRepository,
        private IRestaurantRepository $restaurantRepository,
        private IEventSessionRepository $eventSessionRepository,
        private ICmsContentRepository $cmsService,
    ) {
    }

    /**
     * Returns all raw data needed to render the home page.
     */
    public function getHomePageData(): HomePageData
    {
        $cmsContent = $this->cmsService->getHomePageContent();

        return new HomePageData(
            cmsContent: $cmsContent,
            heroContent: $this->cmsService->getHeroSectionContent('home'),
            globalUiContent: $this->cmsService->getSectionContent('home', 'global_ui'),
            eventTypes: $this->buildEventTypes($cmsContent),
            locations: $this->buildLocations(),
            scheduleDays: $this->buildScheduleDays(),
        );
    }

    /**
     * Builds event type showcase data with precomputed styles.
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
     */
    private function buildSingleEventType(string $slug, array $typesBySlug, array $cmsContent): ?array
    {
        if (!isset($typesBySlug[$slug])) {
            return null;
        }

        $sectionKey = HomeUiConfig::SECTION_MAP[$slug] ?? null;
        if (!$sectionKey || !isset($cmsContent[$sectionKey])) {
            return null;
        }

        $section = $cmsContent[$sectionKey];

        return [
            'slug'       => $slug,
            'title'      => $section[$slug . '_title'] ?? ucfirst($slug),
            'description' => $section[$slug . '_description'] ?? '',
            'button'     => $section[$slug . '_button'] ?? 'Explore Events',
            'image'      => $section[$slug . '_image'] ?? null,
            'darkBg'     => HomeUiConfig::DARK_BG_MAP[$slug] ?? false,
            'badgeClass' => HomeUiConfig::BADGE_COLORS[$slug] ?? 'bg-gray-500',
        ];
    }

    /**
     * Builds locations list from venues and restaurants.
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
    private function buildVenueLocation(Venue $venue): array
    {
        $category = $this->determineVenueCategory($venue->name);

        return [
            'name'       => $venue->name,
            'address'    => $venue->addressLine,
            'category'   => $category,
            'badgeClass' => HomeUiConfig::BADGE_COLORS[$category] ?? 'bg-gray-500',
        ];
    }

    /**
     * Builds location data for a single restaurant.
     */
    private function buildRestaurantLocation(Restaurant $restaurant): array
    {
        return [
            'name'       => $restaurant->name,
            'address'    => $restaurant->addressLine,
            'category'   => 'restaurant',
            'badgeClass' => HomeUiConfig::BADGE_COLORS['restaurant'],
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
        $byType = $this->groupSessionsByType($sessions);

        return [
            'date'       => $date,
            'eventCount' => count($byType),
            'sessions'   => $this->collectSessionsForDisplay($byType),
        ];
    }

    /**
     * Groups sessions by event type slug.
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
     * Collects raw session data grouped by type for mapper formatting.
     */
    private function collectSessionsForDisplay(array $byType): array
    {
        $result = [];

        foreach ($byType as $slug => $typeData) {
            $sessions = $typeData['sessions'];
            $starts   = array_map(fn ($s) => $s->startDateTime->getTimestamp(), $sessions);
            $ends     = array_map(fn ($s) => $s->endDateTime ? $s->endDateTime->getTimestamp() : $s->startDateTime->getTimestamp(), $sessions);

            $result[] = [
                'earliestStart'  => min($starts),
                'latestEnd'      => max($ends),
                'eventTypeSlug'  => $slug,
                'typeName'       => $typeData['typeName'],
                'firstEventTitle' => $sessions[0]->eventTitle ?? '',
            ];
        }

        return $result;
    }

    /**
     * Gets summary title for event type sessions.
     */
    private function getEventSummaryTitle(string $slug, array $sessions): string
    {
        return HomeUiConfig::EVENT_SUMMARY_TITLES[$slug] ?? ($sessions[0]->eventTitle ?? '');
    }

    /**
     * Builds placeholder days when no sessions exist.
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
    private function buildSinglePlaceholderDay(string $date): array
    {
        return [
            'date'       => $date,
            'eventCount' => 0,
            'sessions'   => [],
        ];
    }
}
