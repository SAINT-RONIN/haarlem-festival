<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

final readonly class ScheduleSectionViewModel
{
    /**
     * @param array<ScheduleDayViewModel> $days
     */
    public function __construct(
        public string  $sectionId,
        public string  $title,
        public ?string $year,
        public string  $eventTypeSlug,
        public int     $eventTypeId,
        public string  $filtersButtonText,
        public bool    $showFilters,
        public string  $additionalInfoTitle,
        public string  $additionalInfoBody,
        public bool    $showAdditionalInfo,
        public ?string $eventCountLabel,
        public ?int    $eventCount,
        public bool    $showEventCount,
        public string  $ctaButtonText,
        public string  $payWhatYouLikeText,
        public string  $currencySymbol,
        public string  $noEventsText,
        public array   $days,
    ) {
    }

    public static function fromData(array $data): self
    {
        $cmsContent = $data['cmsContent'];
        $pageSlug = $data['pageSlug'];
        $eventTypeSlug = $data['eventTypeSlug'];
        $eventTypeId = $data['eventTypeId'];
        $rawDays = $data['days'];

        $days = self::mapDays($rawDays);
        $eventCount = array_sum(array_map(fn($day) => count($day->events), $days));

        $title = self::str($cmsContent, 'schedule_title', ucfirst($pageSlug) . ' schedule');
        $year = self::str($cmsContent, 'schedule_year', '2026');
        $eventCountLabel = self::str(
            $cmsContent,
            'schedule_event_count_label',
            self::str($cmsContent, 'schedule_story_count_label', 'Events')
        );
        $showEventCount = ($cmsContent['schedule_show_event_count'] ??
                $cmsContent['schedule_show_story_count'] ?? '1') === '1';

        if ($pageSlug === 'history') {
            $year = null;
            $eventCountLabel = null;
            $showEventCount = false;
        }

        return new self(
            sectionId: $pageSlug . '-schedule',
            title: $title,
            year: $year,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            filtersButtonText: self::str($cmsContent, 'schedule_filters_button_text', 'Filters'),
            showFilters: ($cmsContent['schedule_show_filters'] ?? '1') === '1',
            additionalInfoTitle: self::str($cmsContent, 'schedule_additional_info_title', 'Additional Information:'),
            additionalInfoBody: $cmsContent['schedule_additional_info_body'] ?? '',
            showAdditionalInfo: ($cmsContent['schedule_show_additional_info'] ?? '0') === '1',
            eventCountLabel: $eventCountLabel,
            eventCount: $eventCount,
            showEventCount: $showEventCount,
            ctaButtonText: self::str($cmsContent, 'schedule_cta_button_text', 'Discover'),
            payWhatYouLikeText: self::str($cmsContent, 'schedule_pay_what_you_like_text', 'Pay as you like'),
            currencySymbol: self::str($cmsContent, 'schedule_currency_symbol', '€'),
            noEventsText: self::str($cmsContent, 'schedule_no_events_text', 'No events scheduled'),
            days: $days,
        );
    }

    /**
     * @return ScheduleDayViewModel[]
     */
    private static function mapDays(array $rawDays): array
    {
        $days = [];
        foreach ($rawDays as $day) {
            $events = [];
            foreach ($day['events'] as $event) {
                $events[] = new ScheduleEventCardViewModel(...$event);
            }

            $days[] = new ScheduleDayViewModel(
                dayName: $day['dayName'],
                dateFormatted: $day['dateFormatted'],
                isoDate: $day['isoDate'],
                events: $events,
                isEmpty: $day['isEmpty'],
            );
        }
        return $days;
    }

    private static function str(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
