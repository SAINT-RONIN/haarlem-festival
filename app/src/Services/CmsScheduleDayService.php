<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use App\DTOs\Domain\Filters\EventTypeFilter;
use App\DTOs\Domain\Filters\ScheduleDayConfigFilter;
use App\DTOs\Domain\Pages\ScheduleDaysPageData;
use App\DTOs\Domain\Schedule\GroupedScheduleDayConfigs;
use App\Services\Interfaces\IScheduleDayVisibilityResolver;
use App\Services\Interfaces\ICmsScheduleDayService;

// Extracted from CmsEventsService to isolate schedule-day config from event CRUD.
class CmsScheduleDayService implements ICmsScheduleDayService
{
    public function __construct(
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IScheduleDayVisibilityResolver $visibilityResolver,
    ) {}

    public function getScheduleDaysPageData(): ScheduleDaysPageData
    {
        return new ScheduleDaysPageData(
            eventTypes: $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name')),
            grouped: $this->getGroupedScheduleDayConfigs(),
        );
    }

    /** @return \App\Models\ScheduleDayConfig[] */
    public function getScheduleDayConfigs(): array
    {
        return $this->scheduleDayConfigRepository->findConfigs(
            new ScheduleDayConfigFilter(includeEventTypeName: true, orderBy: 'scope'),
        );
    }

    // Keyed by day-of-week number for O(1) grid lookups.
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs
    {
        $dayConfigs = $this->getScheduleDayConfigs();
        $globalConfigs = [];
        $typeConfigs = [];

        foreach ($dayConfigs as $config) {
            // DB returns dayOfWeek as a string; cast to int for consistent numeric array keys.
            if (!$config->eventTypeId) {
                $globalConfigs[(int) $config->dayOfWeek] = $config;
            } else {
                $typeConfigs[(int) $config->eventTypeId][(int) $config->dayOfWeek] = $config;
            }
        }

        return new GroupedScheduleDayConfigs(global: $globalConfigs, byType: $typeConfigs);
    }

    /**
     * @param ?int $eventTypeId null for global setting, >0 for a specific event type
     * @throws ValidationException When the day number is not in the DayOfWeek enum
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $dayValues = array_map(static fn(DayOfWeek $day): int => $day->value, DayOfWeek::cases());
        if (!in_array($dayOfWeek, $dayValues, true)) {
            throw new ValidationException(['Invalid day of week']);
        }

        $this->scheduleDayConfigRepository->upsert($eventTypeId, $dayOfWeek, $isVisible);
    }

    /** @return int[] */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
