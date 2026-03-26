<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Pages\HomeEventTypeData;
use App\DTOs\Pages\HomeLocationData;
use App\DTOs\Pages\HomePageData;
use App\Models\Restaurant;
use App\Models\Venue;
use App\DTOs\Pages\HomeScheduleDayData;
use App\DTOs\Pages\HomeScheduleSessionData;
use App\Constants\HomeUiConfig;
use App\ViewModels\HomeEventTypeViewModel;
use App\ViewModels\HomeLocationViewModel;
use App\ViewModels\HomePageViewModel;
use App\ViewModels\HomeScheduleDayViewModel;
use App\ViewModels\HomeScheduleSessionViewModel;

/**
 * Transforms HomePageData domain models into the HomePageViewModel consumed by the
 * public home page, including hero, event-type cards, venue map locations, and the schedule grid.
 */
final class HomeMapper
{
    /**
     * Builds the full home-page ViewModel by combining CMS hero/global-UI data
     * with event types, map locations, and the weekly schedule.
     */
    public static function toPageViewModel(HomePageData $data, bool $isLoggedIn): HomePageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'home');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);
        $cms = array_merge($data->cmsContent, CmsMapper::toCmsData($heroData, $globalUi));

        return new HomePageViewModel(
            heroData:     $heroData,
            globalUi:     $globalUi,
            cms:          $cms,
            eventTypes:   self::formatEventTypes($data->eventTypes),
            locations:    self::formatLocations($data->locations),
            scheduleDays: self::formatScheduleDays($data->scheduleDays),
        );
    }

    /**
     * @param HomeEventTypeData[] $eventTypes
     * @return HomeEventTypeViewModel[]
     */
    private static function formatEventTypes(array $eventTypes): array
    {
        return array_map(fn(HomeEventTypeData $t) => new HomeEventTypeViewModel(
            slug:        $t->slug,
            title:       $t->title,
            description: $t->description,
            button:      $t->button,
            image:       $t->image,
            darkBg:      $t->darkBg,
            badgeClass:  HomeUiConfig::EVENT_TYPE_CONFIG[$t->slug]['badgeColor'] ?? 'bg-gray-500',
            imageSrc:    self::resolveEventTypeImage($t->slug, $t->image),
            imageAlt:    self::resolveEventTypeAlt($t->slug, $t->title),
        ), $eventTypes);
    }

    /** Resolves the display image for an event type card — CMS image or fallback. */
    private static function resolveEventTypeImage(string $slug, ?string $cmsImage): string
    {
        if ($cmsImage !== null && $cmsImage !== '') {
            return $cmsImage;
        }

        $fallbacks = [
            'jazz'         => '/assets/Image/Image (Jazz).png',
            'dance'        => '/assets/Image/Image (Dance).png',
            'history'      => '/assets/Image/Image (History).png',
            'restaurant'   => '/assets/Image/Image (Yummy).png',
            'storytelling' => '/assets/Image/Image (Story).png',
        ];

        return $fallbacks[$slug] ?? '/assets/Image/placeholder.png';
    }

    /** Resolves a meaningful alt text for an event type card image. */
    private static function resolveEventTypeAlt(string $slug, string $title): string
    {
        $altTexts = [
            'jazz'         => 'Jazz musicians performing live at Haarlem Festival',
            'dance'        => 'Dancers performing at Haarlem Festival dance event',
            'history'      => 'Historic buildings and walking tour in Haarlem',
            'restaurant'   => 'Delicious food served at Haarlem Festival restaurants',
            'storytelling' => 'Storytelling performance at Haarlem Festival',
        ];

        return $altTexts[$slug] ?? $title . ' event';
    }

    /**
     * @param HomeLocationData[] $locations
     * @return HomeLocationViewModel[]
     */
    private static function formatLocations(array $locations): array
    {
        return array_map(fn(HomeLocationData $l) => new HomeLocationViewModel(
            name:       $l->name,
            address:    $l->address,
            category:   $l->category,
            badgeClass: HomeUiConfig::EVENT_TYPE_CONFIG[$l->category]['badgeColor'] ?? 'bg-gray-500',
        ), $locations);
    }

    /**
     * Formats typed schedule day data into display-ready ViewModels.
     *
     * @param HomeScheduleDayData[] $rawDays
     * @return HomeScheduleDayViewModel[]
     */
    private static function formatScheduleDays(array $rawDays): array
    {
        return array_map([self::class, 'formatSingleDay'], $rawDays);
    }

    private static function formatSingleDay(HomeScheduleDayData $day): HomeScheduleDayViewModel
    {
        $dateObj = new \DateTimeImmutable($day->date);
        $dayName = $dateObj->format('l');
        $dayNumber = $dateObj->format('j');
        $htmlId = 'schedule-day-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $dayName)) . '-' . $dayNumber;

        return new HomeScheduleDayViewModel(
            date:       $day->date,
            dayName:    $dayName,
            dayNumber:  $dayNumber,
            monthShort: strtoupper($dateObj->format('M')),
            isoDate:    $dateObj->format('Y-m-d'),
            eventCount: $day->eventCount,
            sessions:   self::formatSessions($day->sessions),
            htmlId:     $htmlId,
        );
    }

    /**
     * @param HomeScheduleSessionData[] $rawSessions
     * @return HomeScheduleSessionViewModel[]
     */
    private static function formatSessions(array $rawSessions): array
    {
        return array_map([self::class, 'formatSingleSession'], $rawSessions);
    }

    /**
     * Converts a single schedule session into a ViewModel, looking up its display
     * title and border color from HomeUiConfig constants keyed by event-type slug.
     */
    private static function formatSingleSession(HomeScheduleSessionData $session): HomeScheduleSessionViewModel
    {
        $slug = $session->eventTypeSlug;

        return new HomeScheduleSessionViewModel(
            timeLabel:     self::formatSessionTimes($session->earliestStart, $session->latestEnd),
            title:         HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['summaryTitle'] ?? $session->firstEventTitle,
            categoryLabel: $session->typeName,
            borderClass:   HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['scheduleColor'] ?? 'bg-gray-500',
        );
    }

    /** Formats a start-end timestamp pair into "HH:MM -- HH:MM" using an en-dash separator. */
    private static function formatSessionTimes(int $earliestStart, int $latestEnd): string
    {
        return date('H:i', $earliestStart) . " \u{2013} " . date('H:i', $latestEnd);
    }

    /**
     * Maps a CMS section's key-value pairs into a HomeEventTypeData object.
     * Extracted from HomeService so mapping logic lives in the mapper layer.
     *
     * @param array<string, ?string> $section
     */
    public static function toEventTypeData(string $slug, array $section): HomeEventTypeData
    {
        return new HomeEventTypeData(
            slug: $slug,
            title: (string)($section[$slug . '_title'] ?? ucfirst($slug)),
            description: (string)($section[$slug . '_description'] ?? ''),
            button: (string)($section[$slug . '_button'] ?? 'Explore Events'),
            image: $section[$slug . '_image'] ?? null,
            darkBg: HomeUiConfig::EVENT_TYPE_CONFIG[$slug]['darkBg'] ?? false,
        );
    }

    /** Maps a Venue model to a HomeLocationData DTO for the map grid. */
    public static function toVenueLocation(Venue $venue): HomeLocationData
    {
        return new HomeLocationData(
            name: $venue->name,
            address: $venue->addressLine,
            category: self::determineVenueCategory($venue->name),
            lat: null,
            lng: null,
        );
    }

    /** Maps a Restaurant model to a HomeLocationData DTO for the map grid. */
    public static function toRestaurantLocation(Restaurant $restaurant): HomeLocationData
    {
        return new HomeLocationData(
            name: $restaurant->name,
            address: $restaurant->addressLine,
            category: 'restaurant',
            lat: null,
            lng: null,
        );
    }

    /** Flat keyword-to-category map for venue classification. */
    private const KEYWORD_TO_CATEGORY = [
        'patronaat'    => 'jazz',
        'club'         => 'dance',
        'lichtfabriek' => 'dance',
        'slachthuis'   => 'dance',
        'jopenkerk'    => 'dance',
        'caprera'      => 'dance',
        'puncher'      => 'dance',
        'bavo'         => 'history',
        'church'       => 'history',
        'verhalen'     => 'storytelling',
        'schuur'       => 'storytelling',
        'kweek'        => 'storytelling',
        'boom'         => 'storytelling',
        'theater'      => 'storytelling',
    ];

    /**
     * Maps a venue to an event-type category by matching known keywords in
     * the venue name. Falls back to 'history' for unrecognised venues.
     */
    private static function determineVenueCategory(string $venueName): string
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
