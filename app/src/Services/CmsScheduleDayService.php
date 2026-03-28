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
     * Assembles all data needed for the CMS schedule days management page.
     */
    public function getScheduleDaysPageData(): ScheduleDaysPageData
    {
        return new ScheduleDaysPageData(
            eventTypes: $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name')),
            grouped: $this->getGroupedScheduleDayConfigs(),
        );
    }

    /**
     * Returns all schedule day configs (both global and per-event-type), sorted by scope.
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
     * Partitions schedule day configs into global defaults vs. per-event-type overrides,
     * keyed by day-of-week number, for rendering the two-tier visibility grid in the CMS.
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs
    {
        $dayConfigs = $this->getScheduleDayConfigs();
        $globalConfigs = [];
        $typeConfigs = [];

        foreach ($dayConfigs as $config) {
            if (!$config->eventTypeId) {
                $globalConfigs[(int)$config->dayOfWeek] = $config;
            } else {
                $typeConfigs[(int)$config->eventTypeId][(int)$config->dayOfWeek] = $config;
            }
        }

        return new GroupedScheduleDayConfigs(global: $globalConfigs, byType: $typeConfigs);
    }

    /**
     * Sets the visibility of a schedule day.
     *
     * @param ?int $eventTypeId null for global setting, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday, 1=Monday, ..., 6=Saturday
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $dayValues = array_map(static fn (DayOfWeek $day): int => $day->value, DayOfWeek::cases());
        if (!in_array($dayOfWeek, $dayValues, true)) {
            throw new ValidationException(['Invalid day of week']);
        }

        $this->scheduleDayConfigRepository->upsert($eventTypeId, $dayOfWeek, $isVisible);
    }

    /**
     * Determines which days of the week are visible for a given event type.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday) that should be shown
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
