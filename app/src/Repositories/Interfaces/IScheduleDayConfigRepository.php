<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Filters\ScheduleDayConfigFilter;

/**
 * Contract for managing per-day visibility settings on the public schedule.
 * Configs can be global (all event types) or scoped to a specific event type.
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
