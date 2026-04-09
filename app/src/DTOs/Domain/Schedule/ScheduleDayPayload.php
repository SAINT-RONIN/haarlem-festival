<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Per-day domain payload assembled by ScheduleService for the schedule mappers.
 *
 * Carries the raw sessions and the pre-resolved label/history-tour data for that day.
 * The mapper layer (ScheduleDayMapper) is responsible for converting sessions into
 * event card arrays and ViewModels using this payload.
 */
final readonly class ScheduleDayPayload
{
    /**
     * @param SessionWithEvent[]              $sessions           Sessions for this day (merged for History)
     * @param array<int, mixed[]>             $labelsMap          Labels per session id (may differ from global after History merging)
     * @param array<int, array<int, mixed[]>> $historyTourOptions Tour options keyed by primary session id (History only)
     */
    public function __construct(
        public string $dayName,
        public string $isoDate,
        public bool   $isEmpty,
        public array  $sessions,
        public array  $labelsMap,
        public array  $historyTourOptions,
    ) {}
}
