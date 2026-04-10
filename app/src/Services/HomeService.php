<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Filters\EventSessionFilter;
use App\Models\EventType;
use App\DTOs\Domain\Pages\HomePageData;
use App\DTOs\Domain\Pages\HomeScheduleDayData;
use App\DTOs\Domain\Pages\HomeScheduleSessionData;
use App\DTOs\Domain\Pages\HomeEventTypeData;
use App\DTOs\Domain\Pages\HomeLocationData;
use App\DTOs\Domain\Filters\VenueFilter;
use App\Helpers\SessionGroupingHelper;
use App\Models\Venue;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Services\Interfaces\IHomeService;
use App\Constants\HomeUiConfig;

class HomeService extends BaseContentService implements IHomeService
{
    public function __construct(
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IVenueRepository $venueRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly ICmsContentRepository $cmsContentRepository,
        IGlobalContentRepository $globalContentRepo,
    ) {
        parent::__construct($globalContentRepo);
    }

    public function getHomePageData(): HomePageData
    {
        return $this->guardPageLoad(
            fn(): HomePageData => $this->assembleHomePageData(),
            'Failed to load the home page.',
        );
    }

    private function assembleHomePageData(): HomePageData
    {
        // Load all CMS key-value content for the home page (used by event-type cards)
        $cmsContent = $this->cmsContentRepository->getHomePageContent();

        return new HomePageData(
            cmsContent: $cmsContent,
            heroContent: $this->globalContentRepo->findHeroContent('home'),
            globalUiContent: $this->loadGlobalUi(),
            eventTypes: $this->buildEventTypes($cmsContent),
            locations: $this->buildLocations(),
            scheduleDays: $this->buildScheduleDays(),
        );
    }

    /** @param array<string, array<string, ?string>> $cmsContent @return HomeEventTypeData[] */
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

    /** @param EventType[] $types @return array<string, EventType> */
    private function indexTypesBySlug(array $types): array
    {
        $typesBySlug = [];
        foreach ($types as $type) {
            $typesBySlug[$type->slug] = $type;
        }
        return $typesBySlug;
    }

    /** @param array<string, EventType> $typesBySlug @param array<string, array<string, ?string>> $cmsContent */
    private function buildSingleEventType(string $slug, array $typesBySlug, array $cmsContent): ?HomeEventTypeData
    {
        if (!isset($typesBySlug[$slug])) {
            return null;
        }

        $section = $this->findCmsSection($slug, $cmsContent);
        if ($section === null) {
            return null;
        }

        return new HomeEventTypeData(
            slug: $slug,
            title: (string) ($section[$slug . '_title'] ?? ucfirst($slug)),
            description: (string) ($section[$slug . '_description'] ?? ''),
            button: (string) ($section[$slug . '_button'] ?? 'Explore Events'),
            image: $section[$slug . '_image'] ?? null,
            darkBg: HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['darkBg'] ?? false,
        );
    }

    private function findCmsSection(string $slug, array $cmsContent): ?array
    {
        $sectionKey = HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['sectionKey'] ?? null;

        if (!$sectionKey || !isset($cmsContent[$sectionKey])) {
            return null;
        }

        return $cmsContent[$sectionKey];
    }


    /** @return HomeLocationData[] */
    private function buildLocations(): array
    {
        $locations = [];

        foreach ($this->venueRepository->findVenues(new VenueFilter(isActive: true)) as $venue) {
            $locations[] = $this->buildVenueLocation($venue);
        }

        return $locations;
    }

    /** @return HomeScheduleDayData[] */
    private function buildScheduleDays(): array
    {
        $sessions = $this->eventSessionRepository->findSessions(new EventSessionFilter(
            isActive: true,
            eventIsActive: true,
            includeCancelled: false,
            orderBy: 'es.StartDateTime ASC',
        ))->sessions;
        $grouped = SessionGroupingHelper::groupByDate($sessions);

        if (empty($grouped)) {
            return $this->buildPlaceholderDays();
        }

        return $this->buildScheduleDaysFromGrouped($grouped);
    }

    /** @param array<string, \App\DTOs\Schedule\SessionWithEvent[]> $grouped @return HomeScheduleDayData[] */
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

    /** @param \App\DTOs\Schedule\SessionWithEvent[] $sessions */
    private function buildDayData(string $date, array $sessions): HomeScheduleDayData
    {
        $byType = $this->groupSessionsByType($sessions);

        return new HomeScheduleDayData(
            date: $date,
            eventCount: count($byType),
            sessions: $this->collectSessionsForDisplay($byType),
        );
    }

    /** @param \App\DTOs\Schedule\SessionWithEvent[] $sessions @return array<string, array{typeName: string, typeSlug: string, sessions: \App\DTOs\Schedule\SessionWithEvent[]}> */
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

    /** @return HomeScheduleSessionData[] */
    private function collectSessionsForDisplay(array $byType): array
    {
        $result = [];

        foreach ($byType as $slug => $typeData) {
            $sessions = $typeData['sessions'];
            $starts   = array_map(fn($s) => $s->startDateTime->getTimestamp(), $sessions);
            $ends     = array_map(fn($s) => $s->endDateTime ? $s->endDateTime->getTimestamp() : $s->startDateTime->getTimestamp(), $sessions);

            $result[] = new HomeScheduleSessionData(
                earliestStart: min($starts),
                latestEnd: max($ends),
                eventTypeSlug: $slug,
                firstEventTitle: (string) ($sessions[0]->eventTitle ?? ''),
                typeName: (string) ($typeData['typeName']),
            );
        }

        return $result;
    }

    /** @return HomeScheduleDayData[] */
    private function buildPlaceholderDays(): array
    {
        $result = [];
        foreach (HomeUiConfig::PLACEHOLDER_DATES as $date) {
            $result[] = $this->buildSinglePlaceholderDay($date);
        }
        return $result;
    }

    private function buildSinglePlaceholderDay(string $date): HomeScheduleDayData
    {
        return new HomeScheduleDayData(date: $date, eventCount: 0, sessions: []);
    }

    private function buildVenueLocation(Venue $venue): HomeLocationData
    {
        return new HomeLocationData(
            name: $venue->name,
            address: $venue->addressLine,
            category: $this->determineVenueCategory($venue->name),
            lat: null,
            lng: null,
        );
    }

    /** Flat keyword-to-category map for venue classification. */
    private const KEYWORD_TO_CATEGORY = [
        'patronaat' => 'jazz',
        'club' => 'dance',
        'lichtfabriek' => 'dance',
        'slachthuis' => 'dance',
        'jopenkerk' => 'dance',
        'caprera' => 'dance',
        'puncher' => 'dance',
        'bavo' => 'history',
        'church' => 'history',
        'verhalen' => 'storytelling',
        'schuur' => 'storytelling',
        'kweek' => 'storytelling',
        'boom' => 'storytelling',
        'theater' => 'storytelling',
    ];

    private function determineVenueCategory(string $venueName): string
    {
        $name = strtolower($venueName);

        foreach (self::KEYWORD_TO_CATEGORY as $keyword => $category) {
            if (str_contains($name, $keyword)) {
                return $category;
            }
        }

        return 'history';
    }
}
