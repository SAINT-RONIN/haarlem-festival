<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IScheduleDayVisibilityResolver
{
    /**
     * @return int[] Day numbers (0=Sunday through 6=Saturday) that are visible
     */
    public function getVisibleDays(?int $eventTypeId = null): array;
}
