<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Content\ScheduleSectionContent;

/**
 * Maps raw CMS arrays into Schedule page content models.
 */
final class ScheduleContentMapper
{
    /** Maps one raw CMS content array into the typed content object used by the schedule page. */
    public static function mapScheduleSection(array $raw): ScheduleSectionContent
    {
        return new ScheduleSectionContent(
            ...self::mapButtonTexts($raw),
            ...self::mapHeaderTexts($raw),
            ...self::mapSectionSettings($raw),
            ...self::mapServiceFields($raw),
            ...self::mapFilterLabels($raw),
        );
    }

    /**
     * Extracts the action button texts used by the schedule UI.
     *
     * @return array<string, ?string>
     */
    private static function mapButtonTexts(array $raw): array
    {
        return self::mapValues($raw, [
            'scheduleCtaButtonText' => 'schedule_cta_button_text',
            'scheduleConfirmText' => 'schedule_confirm_text',
            'scheduleAddingText' => 'schedule_adding_text',
            'scheduleSuccessText' => 'schedule_success_text',
        ]);
    }

    /**
     * Extracts the page heading texts and event counters shown above the schedule.
     *
     * @return array<string, ?string>
     */
    private static function mapHeaderTexts(array $raw): array
    {
        return self::mapValues($raw, [
            'scheduleTitle' => 'schedule_title',
            'scheduleYear' => 'schedule_year',
            'scheduleEventCountLabel' => 'schedule_event_count_label',
            'scheduleStoryCountLabel' => 'schedule_story_count_label',
            'scheduleShowEventCount' => 'schedule_show_event_count',
            'scheduleShowStoryCount' => 'schedule_show_story_count',
        ]);
    }

    /**
     * Extracts section-level settings such as filter visibility and extra info text.
     *
     * @return array<string, ?string>
     */
    private static function mapSectionSettings(array $raw): array
    {
        return self::mapValues($raw, [
            'scheduleFiltersButtonText' => 'schedule_filters_button_text',
            'scheduleShowFilters' => 'schedule_show_filters',
            'scheduleAdditionalInfoTitle' => 'schedule_additional_info_title',
            'scheduleAdditionalInfoBody' => 'schedule_additional_info_body',
            'scheduleShowAdditionalInfo' => 'schedule_show_additional_info',
            'schedulePayWhatYouLikeText' => 'schedule_pay_what_you_like_text',
            'scheduleCurrencySymbol' => 'schedule_currency_symbol',
            'scheduleNoEventsText' => 'schedule_no_events_text',
            'scheduleFilterResetText' => 'schedule_filter_reset_text',
        ]);
    }

    /**
     * Extracts service-specific fields that do not fit into the other schedule groups.
     *
     * @return array<string, ?string>
     */
    private static function mapServiceFields(array $raw): array
    {
        return self::mapValues($raw, [
            'scheduleStartPoint' => 'schedule_start_point',
            'scheduleHistoryGroupTicket' => 'schedule_history_group_ticket',
        ]);
    }

    /**
     * Extracts all filter labels so the frontend can render consistent filter text.
     *
     * @return array<string, ?string>
     */
    private static function mapFilterLabels(array $raw): array
    {
        return self::mapValues($raw, [
            'scheduleFilterAllLabel' => 'schedule_filter_all_label',
            'scheduleFilterDayLabel' => 'schedule_filter_day_label',
            'scheduleFilterTimeRangeLabel' => 'schedule_filter_time_range_label',
            'scheduleFilterMorningLabel' => 'schedule_filter_morning_label',
            'scheduleFilterAfternoonLabel' => 'schedule_filter_afternoon_label',
            'scheduleFilterEveningLabel' => 'schedule_filter_evening_label',
            'scheduleFilterPriceTypeLabel' => 'schedule_filter_price_type_label',
            'scheduleFilterFreeLabel' => 'schedule_filter_free_label',
            'scheduleFilterPaidLabel' => 'schedule_filter_paid_label',
            'scheduleFilterPayAsYouLikeLabel' => 'schedule_filter_pay_as_you_like_label',
            'scheduleFilterLanguageLabel' => 'schedule_filter_language_label',
            'scheduleFilterEnglishLabel' => 'schedule_filter_english_label',
            'scheduleFilterDutchLabel' => 'schedule_filter_dutch_label',
            'scheduleFilterAgeGroupLabel' => 'schedule_filter_age_group_label',
            'scheduleFilterAllAgesLabel' => 'schedule_filter_all_ages_label',
            'scheduleFilterAge4Label' => 'schedule_filter_age_4_label',
            'scheduleFilterAge10Label' => 'schedule_filter_age_10_label',
            'scheduleFilterAge12Label' => 'schedule_filter_age_12_label',
            'scheduleFilterAge16Label' => 'schedule_filter_age_16_label',
            'scheduleFilterVenueLabel' => 'schedule_filter_venue_label',
        ]);
    }

    /**
     * Copies values from the raw CMS array into the property names expected by the content model.
     *
     * @param array<string, string> $fieldMap
     * @return array<string, ?string>
     */
    private static function mapValues(array $raw, array $fieldMap): array
    {
        $values = [];

        foreach ($fieldMap as $field => $key) {
            // Missing CMS keys are allowed here, so the content model receives null instead of crashing.
            $values[$field] = $raw[$key] ?? null;
        }

        return $values;
    }
}
