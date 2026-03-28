<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Content\ScheduleSectionContent;

/**
 * Maps raw CMS arrays into Schedule page content models.
 */
final class ScheduleContentMapper
{
    /** Maps raw CMS data to a ScheduleSectionContent model. */
    public static function mapScheduleSection(array $raw): ScheduleSectionContent
    {
        return new ScheduleSectionContent(
            // ── Button texts ──
            scheduleCtaButtonText: $raw['schedule_cta_button_text'] ?? null,
            scheduleConfirmText: $raw['schedule_confirm_text'] ?? null,
            scheduleAddingText: $raw['schedule_adding_text'] ?? null,
            scheduleSuccessText: $raw['schedule_success_text'] ?? null,

            // ── Header texts ──
            scheduleTitle: $raw['schedule_title'] ?? null,
            scheduleYear: $raw['schedule_year'] ?? null,
            scheduleEventCountLabel: $raw['schedule_event_count_label'] ?? null,
            scheduleStoryCountLabel: $raw['schedule_story_count_label'] ?? null,
            scheduleShowEventCount: $raw['schedule_show_event_count'] ?? null,
            scheduleShowStoryCount: $raw['schedule_show_story_count'] ?? null,

            // ── Section settings ──
            scheduleFiltersButtonText: $raw['schedule_filters_button_text'] ?? null,
            scheduleShowFilters: $raw['schedule_show_filters'] ?? null,
            scheduleAdditionalInfoTitle: $raw['schedule_additional_info_title'] ?? null,
            scheduleAdditionalInfoBody: $raw['schedule_additional_info_body'] ?? null,
            scheduleShowAdditionalInfo: $raw['schedule_show_additional_info'] ?? null,
            schedulePayWhatYouLikeText: $raw['schedule_pay_what_you_like_text'] ?? null,
            scheduleCurrencySymbol: $raw['schedule_currency_symbol'] ?? null,
            scheduleNoEventsText: $raw['schedule_no_events_text'] ?? null,
            scheduleFilterResetText: $raw['schedule_filter_reset_text'] ?? null,

            // ── Service-only fields ──
            scheduleStartPoint: $raw['schedule_start_point'] ?? null,
            scheduleHistoryGroupTicket: $raw['schedule_history_group_ticket'] ?? null,

            // ── Filter labels ──
            scheduleFilterAllLabel: $raw['schedule_filter_all_label'] ?? null,
            scheduleFilterDayLabel: $raw['schedule_filter_day_label'] ?? null,
            scheduleFilterTimeRangeLabel: $raw['schedule_filter_time_range_label'] ?? null,
            scheduleFilterMorningLabel: $raw['schedule_filter_morning_label'] ?? null,
            scheduleFilterAfternoonLabel: $raw['schedule_filter_afternoon_label'] ?? null,
            scheduleFilterEveningLabel: $raw['schedule_filter_evening_label'] ?? null,
            scheduleFilterPriceTypeLabel: $raw['schedule_filter_price_type_label'] ?? null,
            scheduleFilterFreeLabel: $raw['schedule_filter_free_label'] ?? null,
            scheduleFilterPaidLabel: $raw['schedule_filter_paid_label'] ?? null,
            scheduleFilterPayAsYouLikeLabel: $raw['schedule_filter_pay_as_you_like_label'] ?? null,
            scheduleFilterLanguageLabel: $raw['schedule_filter_language_label'] ?? null,
            scheduleFilterEnglishLabel: $raw['schedule_filter_english_label'] ?? null,
            scheduleFilterDutchLabel: $raw['schedule_filter_dutch_label'] ?? null,
            scheduleFilterAgeGroupLabel: $raw['schedule_filter_age_group_label'] ?? null,
            scheduleFilterAllAgesLabel: $raw['schedule_filter_all_ages_label'] ?? null,
            scheduleFilterAge4Label: $raw['schedule_filter_age_4_label'] ?? null,
            scheduleFilterAge10Label: $raw['schedule_filter_age_10_label'] ?? null,
            scheduleFilterAge12Label: $raw['schedule_filter_age_12_label'] ?? null,
            scheduleFilterAge16Label: $raw['schedule_filter_age_16_label'] ?? null,
            scheduleFilterVenueLabel: $raw['schedule_filter_venue_label'] ?? null,
        );
    }
}
