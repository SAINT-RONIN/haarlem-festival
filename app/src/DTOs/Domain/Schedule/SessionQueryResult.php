<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Typed result of a session query.
 *
 * Returned by IEventSessionRepository::findSessions().
 * When groupByDay was not requested, days is empty.
 */
final readonly class SessionQueryResult
{
    /**
     * @param SessionWithEvent[] $sessions
     * @param ScheduleDayData[]  $days
     */
    public function __construct(
        public array $sessions,
        public array $days = [],
    ) {}
}
