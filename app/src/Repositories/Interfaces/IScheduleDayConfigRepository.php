<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for ScheduleDayConfig repository.
 */
interface IScheduleDayConfigRepository
{
    /**
     * Returns all schedule day configurations.
     *
     * @return array List of all configurations
     */
    public function findAll(): array;

    /**
     * Gets global settings (EventTypeId = 0).
     *
     * @return array List of global day settings
     */
    public function findGlobalSettings(): array;

    /**
     * Gets settings for a specific event type.
     *
     * @param int $eventTypeId The event type ID
     * @return array List of day settings for the event type
     */
    public function findByEventTypeId(int $eventTypeId): array;

    /**
     * Upserts a schedule day visibility setting.
     *
     * @param int $eventTypeId 0 for global, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday through 6=Saturday
     * @param bool $isVisible Whether the day is visible
     */
    public function upsert(int $eventTypeId, int $dayOfWeek, bool $isVisible): void;
}
