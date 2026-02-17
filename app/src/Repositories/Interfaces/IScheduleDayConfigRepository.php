<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for ScheduleDayConfig repository.
 */
interface IScheduleDayConfigRepository
{
    /**
     * Returns schedule day configurations using optional filters.
     *
     * @param array{
     *   eventTypeId?: int,
     *   includeEventTypeName?: bool,
     *   includeGlobal?: bool,
     *   orderBy?: string
     * } $filters
     * @return array List of matching configurations
     */
    public function findConfigs(array $filters = []): array;

    /**
     * Upserts a schedule day visibility setting.
     *
     * @param int $eventTypeId 0 for global, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday through 6=Saturday
     * @param bool $isVisible Whether the day is visible
     */
    public function upsert(int $eventTypeId, int $dayOfWeek, bool $isVisible): void;
}
