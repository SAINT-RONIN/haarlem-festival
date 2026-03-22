<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\HomeEventTypeData;
use App\Models\HomeLocationData;
use App\Models\HomePageData;
use App\Models\HomeScheduleDayData;
use App\Models\HomeScheduleSessionData;
use App\Utils\HomeUiConfig;
use App\ViewModels\HomeEventTypeViewModel;
use App\ViewModels\HomeLocationViewModel;
use App\ViewModels\HomePageViewModel;
use App\ViewModels\HomeScheduleDayViewModel;
use App\ViewModels\HomeScheduleSessionViewModel;

final class HomeMapper
{
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
            badgeClass:  $t->badgeClass,
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
            badgeClass: $l->badgeClass,
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

    private static function formatSessionTimes(int $earliestStart, int $latestEnd): string
    {
        return date('H:i', $earliestStart) . " \u{2013} " . date('H:i', $latestEnd);
    }
}
