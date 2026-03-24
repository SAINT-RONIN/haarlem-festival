<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ScheduleDayConfigFilter;

/**
 * Interface for ScheduleDayConfig repository.
 */
interface IScheduleDayConfigRepository
{
    /**
     * Returns schedule day configurations using optional filters.
     *
     * @return \App\Models\ScheduleDayConfig[] List of matching configurations
     */
    public function findConfigs(ScheduleDayConfigFilter $filter = new ScheduleDayConfigFilter()): array;

    /**
     * Upserts a schedule day visibility setting.
     *
     * @param ?int $eventTypeId null for global, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday through 6=Saturday
     * @param bool $isVisible Whether the day is visible
     */
    public function upsert(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void;
}
