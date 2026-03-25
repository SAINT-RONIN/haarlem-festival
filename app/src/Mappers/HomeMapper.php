<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Pages\HomeEventTypeData;
use App\DTOs\Pages\HomeLocationData;
use App\DTOs\Pages\HomePageData;
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
            badgeClass:  HomeUiConfig::BADGE_COLORS[$t->slug] ?? 'bg-gray-500',
        ), $eventTypes);
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
            badgeClass: HomeUiConfig::BADGE_COLORS[$l->category] ?? 'bg-gray-500',
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

        return new HomeScheduleDayViewModel(
            date:       $day->date,
            dayName:    $dateObj->format('l'),
            dayNumber:  $dateObj->format('j'),
            monthShort: strtoupper($dateObj->format('M')),
            isoDate:    $dateObj->format('Y-m-d'),
            eventCount: $day->eventCount,
            sessions:   self::formatSessions($day->sessions),
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
            title:         HomeUiConfig::EVENT_SUMMARY_TITLES[$slug] ?? $session->firstEventTitle,
            categoryLabel: $session->typeName,
            borderClass:   HomeUiConfig::SCHEDULE_COLORS[$slug] ?? 'bg-gray-500',
        );
    }

    /** Formats a start-end timestamp pair into "HH:MM -- HH:MM" using an en-dash separator. */
    private static function formatSessionTimes(int $earliestStart, int $latestEnd): string
    {
        return date('H:i', $earliestStart) . " \u{2013} " . date('H:i', $latestEnd);
    }
}
