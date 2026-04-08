<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Resolved CMS display strings passed through the schedule card pipeline.
 * Created once in ScheduleService and threaded down to the card builder methods.
 */
final readonly class ScheduleDisplayStrings
{
    public function __construct(
        public string $ctaButtonText,
        public string $payWhatYouLikeText,
        public string $currencySymbol,
        public string $startPoint,
        public string $groupTicketFallback,
    ) {}
}
