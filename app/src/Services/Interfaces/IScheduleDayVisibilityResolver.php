<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Determines which days of the week are visible for a given event type.
 */
interface IScheduleDayVisibilityResolver
{
    /**
     * Returns day numbers that should be shown for the given event type.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday) that are visible
     */
    public function getVisibleDays(?int $eventTypeId = null): array;
}
