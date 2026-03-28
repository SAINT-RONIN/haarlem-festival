<?php

declare(strict_types=1);

namespace App\Content;

/**
 * CMS content for the schedule section — includes all configurable labels,
 * filter texts, toggle flags, and display strings used by ScheduleMapper.
 * Hydrated from CMS key-value pairs via ScheduleContentMapper.
 */
final readonly class ScheduleSectionContent
{
    public function __construct(
        // ── Button texts ──
        public ?string $scheduleCtaButtonText,
        public ?string $scheduleConfirmText,
        public ?string $scheduleAddingText,
        public ?string $scheduleSuccessText,

        // ── Header texts ──
        public ?string $scheduleTitle,
        public ?string $scheduleYear,
        public ?string $scheduleEventCountLabel,
        public ?string $scheduleStoryCountLabel,
        public ?string $scheduleShowEventCount,
        public ?string $scheduleShowStoryCount,

        // ── Section settings ──
        public ?string $scheduleFiltersButtonText,
        public ?string $scheduleShowFilters,
        public ?string $scheduleAdditionalInfoTitle,
        public ?string $scheduleAdditionalInfoBody,
        public ?string $scheduleShowAdditionalInfo,
        public ?string $schedulePayWhatYouLikeText,
        public ?string $scheduleCurrencySymbol,
        public ?string $scheduleNoEventsText,
        public ?string $scheduleFilterResetText,

        // ── Service-only fields ──
        public ?string $scheduleStartPoint,
        public ?string $scheduleHistoryGroupTicket,

        // ── Filter labels ──
        public ?string $scheduleFilterAllLabel,
        public ?string $scheduleFilterDayLabel,
        public ?string $scheduleFilterTimeRangeLabel,
        public ?string $scheduleFilterMorningLabel,
        public ?string $scheduleFilterAfternoonLabel,
        public ?string $scheduleFilterEveningLabel,
        public ?string $scheduleFilterPriceTypeLabel,
        public ?string $scheduleFilterFreeLabel,
        public ?string $scheduleFilterPaidLabel,
        public ?string $scheduleFilterPayAsYouLikeLabel,
        public ?string $scheduleFilterLanguageLabel,
        public ?string $scheduleFilterEnglishLabel,
        public ?string $scheduleFilterDutchLabel,
        public ?string $scheduleFilterAgeGroupLabel,
        public ?string $scheduleFilterAllAgesLabel,
        public ?string $scheduleFilterAge4Label,
        public ?string $scheduleFilterAge10Label,
        public ?string $scheduleFilterAge12Label,
        public ?string $scheduleFilterAge16Label,
        public ?string $scheduleFilterVenueLabel,
    ) {
    }
}
