<?php

declare(strict_types=1);

namespace App\Helpers;

use App\DTOs\Schedule\SessionWithEvent;

/**
 * Groups SessionWithEvent arrays by their pre-computed session date.
 */
final class SessionGroupingHelper
{
    /**
     * Groups sessions by their Y-m-d session date string.
     *
     * @param SessionWithEvent[] $sessions
     * @return array<string, SessionWithEvent[]>
     */
    public static function groupByDate(array $sessions): array
    {
        $grouped = [];
        foreach ($sessions as $session) {
            $grouped[$session->sessionDate][] = $session;
        }
        return $grouped;
    }
}
