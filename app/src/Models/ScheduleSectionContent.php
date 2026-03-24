<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for the schedule filter section.
 * Hydrated from CMS key-value pairs.
 */
final readonly class ScheduleSectionContent
{
    public function __construct(
        public ?string $scheduleCtaButtonText,
        public ?string $schedulePayWhatYouLikeText,
        public ?string $scheduleCurrencySymbol,
        public ?string $scheduleStartPoint,
        public ?string $scheduleHistoryGroupTicket,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            scheduleCtaButtonText: $raw['schedule_cta_button_text'] ?? null,
            schedulePayWhatYouLikeText: $raw['schedule_pay_what_you_like_text'] ?? null,
            scheduleCurrencySymbol: $raw['schedule_currency_symbol'] ?? null,
            scheduleStartPoint: $raw['schedule_start_point'] ?? null,
            scheduleHistoryGroupTicket: $raw['schedule_history_group_ticket'] ?? null,
        );
    }
}
