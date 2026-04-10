<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Pages\ScheduleDaysPageData;
use App\DTOs\Domain\Schedule\GroupedScheduleDayConfigs;
use App\Exceptions\ValidationException;

/**
 * Contract for CMS schedule day visibility management.
 */
interface ICmsScheduleDayService
{
    /**
     * Assembles all data needed for the CMS schedule days management page.
     */
    public function getScheduleDaysPageData(): ScheduleDaysPageData;

    /**
     * Gets all schedule day visibility configurations.
     *
     * @return \App\Models\ScheduleDayConfig[]
     */
    public function getScheduleDayConfigs(): array;

    /**
     * Gets schedule day configs grouped into global and type-specific buckets.
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs;

    /**
     * Sets the visibility of a schedule day.
     *
     * @param ?int $eventTypeId null for global setting, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday, 1=Monday, ..., 6=Saturday
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void;

    /**
     * Returns the day-of-week numbers visible for a given event type.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday)
     */
    public function getVisibleDays(?int $eventTypeId = null): array;
}
