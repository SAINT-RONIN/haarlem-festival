<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\ScheduleSectionContent;

/**
 * Maps raw CMS arrays into Schedule page content models.
 */
final class ScheduleContentMapper
{
    /** Maps raw CMS data to a ScheduleSectionContent model. */
    public static function mapScheduleSection(array $raw): ScheduleSectionContent
    {
        return new ScheduleSectionContent(
            scheduleCtaButtonText: $raw['schedule_cta_button_text'] ?? null,
            schedulePayWhatYouLikeText: $raw['schedule_pay_what_you_like_text'] ?? null,
            scheduleCurrencySymbol: $raw['schedule_currency_symbol'] ?? null,
            scheduleStartPoint: $raw['schedule_start_point'] ?? null,
            scheduleHistoryGroupTicket: $raw['schedule_history_group_ticket'] ?? null,
        );
    }
}
