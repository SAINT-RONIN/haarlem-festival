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
    ) {
    }
}
