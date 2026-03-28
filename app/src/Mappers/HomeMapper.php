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
use App\ViewModels\HomeEventsHeaderViewModel;
use App\ViewModels\HomeExploreBannerViewModel;
use App\ViewModels\HomeIntroSectionViewModel;
use App\ViewModels\HomeLocationsSectionViewModel;
use App\ViewModels\HomeLocationViewModel;
use App\ViewModels\HomePageViewModel;
use App\ViewModels\HomeScheduleSectionViewModel;
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

        return new HomePageViewModel(
            heroData:     $heroData,
            globalUi:     $globalUi,
            exploreBanner: self::buildExploreBanner($data->cmsContent),
            introSection: self::buildIntroSection($data->cmsContent),
            eventsHeader: self::buildEventsHeader($data->cmsContent),
            locationsSection: self::buildLocationsSection($data->cmsContent),
            schedulePreviewSection: self::buildSchedulePreviewSection($data->cmsContent),
            eventTypes:   self::formatEventTypes($data->eventTypes),
            locations:    self::formatLocations($data->locations),
            scheduleDays: self::formatScheduleDays($data->scheduleDays),
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private static function buildExploreBanner(array $cmsContent): HomeExploreBannerViewModel
    {
        $section = self::section($cmsContent, 'banner_section');

        return new HomeExploreBannerViewModel(
            title: self::value($section, 'banner_main_title'),
            subtitle: self::value($section, 'banner_subtitle'),
            backgroundImageUrl: self::value($section, 'banner_background_image', '/assets/Image/explore-incoming-events.png'),
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private static function buildIntroSection(array $cmsContent): HomeIntroSectionViewModel
    {
        $section = self::section($cmsContent, 'about_section');

        return new HomeIntroSectionViewModel(
            title: self::value($section, 'about_main_title'),
            tagline: self::value($section, 'about_tagline'),
            descriptionHtml: self::value($section, 'about_description'),
            buttonLabel: self::value($section, 'about_button'),
            buttonUrl: '#schedule',
            imageUrl: self::value($section, 'about_image', '/assets/Image/what-is-haarlem.png'),
            imageAlt: 'Aerial view of Haarlem city center during the festival, showing historic architecture and event venues',
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private static function buildEventsHeader(array $cmsContent): HomeEventsHeaderViewModel
    {
        $section = self::section($cmsContent, 'events_overview_header');

        return new HomeEventsHeaderViewModel(
            title: self::value($section, 'events_main_title'),
            subtitle: self::value($section, 'events_subtitle'),
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private static function buildLocationsSection(array $cmsContent): HomeLocationsSectionViewModel
    {
        $section = self::section($cmsContent, 'venue_map_section');

        return new HomeLocationsSectionViewModel(
            title: self::value($section, 'venue_main_title'),
            filterLabel: self::value($section, 'venue_filter_label'),
            filterTitle: self::value($section, 'venue_filter_title'),
            allLabel: self::value($section, 'venue_filter_all', 'All'),
            jazzLabel: self::value($section, 'venue_filter_jazz', 'Jazz'),
            danceLabel: self::value($section, 'venue_filter_dance', 'Dance'),
            historyLabel: self::value($section, 'venue_filter_history', 'History'),
            restaurantsLabel: self::value($section, 'venue_filter_restaurants', 'Restaurants'),
            storiesLabel: self::value($section, 'venue_filter_stories', 'Stories'),
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     */
    private static function buildSchedulePreviewSection(array $cmsContent): HomeScheduleSectionViewModel
    {
        $section = self::section($cmsContent, 'schedule_section');

        return new HomeScheduleSectionViewModel(
            title: self::value($section, 'schedule_main_title'),
            subtitlePrimary: self::value($section, 'schedule_subtitle_1'),
            subtitleSecondary: self::value($section, 'schedule_subtitle_2'),
        );
    }

    /**
     * @param array<string, array<string, ?string>> $cmsContent
     * @return array<string, ?string>
     */
    private static function section(array $cmsContent, string $key): array
    {
        return $cmsContent[$key] ?? [];
    }

    /**
     * @param array<string, ?string> $section
     */
    private static function value(array $section, string $key, string $default = ''): string
    {
        return (string)($section[$key] ?? $default);
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

}
