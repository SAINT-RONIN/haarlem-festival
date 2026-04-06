<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use App\DTOs\Filters\EventTypeFilter;
use App\DTOs\Filters\ScheduleDayConfigFilter;
use App\DTOs\Pages\ScheduleDaysPageData;
use App\DTOs\Schedule\GroupedScheduleDayConfigs;
use App\Schedule\Interfaces\IScheduleDayVisibilityResolver;
use App\Services\Interfaces\ICmsScheduleDayService;

/**
 * CMS-side schedule day visibility management.
 *
 * Extracted from CmsEventsService to reduce its dependency count and isolate
 * schedule day configuration logic from event CRUD operations.
 */
class CmsScheduleDayService implements ICmsScheduleDayService
{
    public function __construct(
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IScheduleDayVisibilityResolver $visibilityResolver,
    ) {
    }

    /**
     * Loads everything the schedule-day configuration screen needs and returns it as one bundle.
     *
     * The event types list is used for the filter dropdown at the top of the page.
     * The grouped configs object drives the visibility grid that shows which days are on or off.
     */
    public function getScheduleDaysPageData(): ScheduleDaysPageData
    {
        return new ScheduleDaysPageData(
            eventTypes: $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name')),
            grouped: $this->getGroupedScheduleDayConfigs(),
        );
    }

    /**
     * Returns all schedule day configs, ordered by scope so global entries come first.
     *
     * Both global configs (no event type) and per-type configs are included.
     * The event type name is eagerly loaded so callers don't need a second query.
     *
     * @return \App\Models\ScheduleDayConfig[]
     */
    public function getScheduleDayConfigs(): array
    {
        return $this->scheduleDayConfigRepository->findConfigs(
            new ScheduleDayConfigFilter(includeEventTypeName: true, orderBy: 'scope'),
        );
    }

    /**
     * Splits all schedule day configs into two buckets: global defaults and per-event-type overrides.
     *
     * A global config has no event type ID and applies to all event types that don't have their own
     * override. A type-specific config overrides the global setting for one event type only.
     * Both buckets are keyed by day-of-week number so the grid can look up each cell in O(1).
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs
    {
        $dayConfigs = $this->getScheduleDayConfigs();
        $globalConfigs = [];
        $typeConfigs = [];

        foreach ($dayConfigs as $config) {
            // A missing eventTypeId means this config applies to every event type (global rule).
            if (!$config->eventTypeId) {
                // Cast to int: the DB returns dayOfWeek as a string, but array keys should be numbers.
                $globalConfigs[(int)$config->dayOfWeek] = $config;
            } else {
                // Cast to int for the same reason — consistent numeric keys for both dimensions.
                $typeConfigs[(int)$config->eventTypeId][(int)$config->dayOfWeek] = $config;
            }
        }

        return new GroupedScheduleDayConfigs(global: $globalConfigs, byType: $typeConfigs);
    }

    /**
     * Saves the visibility flag for one day on a schedule, either globally or for a specific event type.
     *
     * The day number is validated before the write because a bad value from the client
     * would create a DB row with an invalid key that is invisible to the rest of the system.
     *
     * @param ?int $eventTypeId null for global setting, >0 for a specific event type
     * @throws ValidationException When the day number is not in the DayOfWeek enum
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        // Build the list of valid day numbers from the enum so we never hardcode 1-7 here.
        $dayValues = array_map(static fn (DayOfWeek $day): int => $day->value, DayOfWeek::cases());
        if (!in_array($dayOfWeek, $dayValues, true)) {
            throw new ValidationException(['Invalid day of week']);
        }

        $this->scheduleDayConfigRepository->upsert($eventTypeId, $dayOfWeek, $isVisible);
    }

    /**
     * Returns the day numbers that are visible for a given event type.
     *
     * Passing null returns the days visible under the global schedule.
     * Passing a specific event type ID returns the days for that type, falling back
     * to the global config for any day that has no type-specific override.
     *
     * @return int[]
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
