<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Filters\ScheduleDayConfigFilter;
use App\Enums\DayOfWeek;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use App\Services\Interfaces\IScheduleDayVisibilityResolver;

final class ScheduleDayVisibilityResolver implements IScheduleDayVisibilityResolver
{
    public function __construct(
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
    ) {}

    /** @return int[] Day numbers (0=Sunday through 6=Saturday) that are visible */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        return $this->mergeVisibilitySettings(
            $this->loadGlobalDaySettings(),
            $this->loadTypeDaySettings($eventTypeId),
        );
    }

    private function loadGlobalDaySettings(): array
    {
        return $this->loadDaySettingsForScope(0);
    }

    private function loadTypeDaySettings(?int $eventTypeId): array
    {
        if ($eventTypeId === null) {
            return [];
        }

        return $this->loadDaySettingsForScope($eventTypeId);
    }

    private function loadDaySettingsForScope(int $eventTypeId): array
    {
        $settings = [];

        foreach ($this->scheduleDayConfigRepository->findConfigs(
            new ScheduleDayConfigFilter(eventTypeId: $eventTypeId, orderBy: 'day'),
        ) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }

        return $settings;
    }

    /** @param array<int, bool> $globalSettings @param array<int, bool> $typeSettings @return int[] */
    private function mergeVisibilitySettings(array $globalSettings, array $typeSettings): array
    {
        $visibleDays = [];

        foreach (DayOfWeek::cases() as $day) {
            if ($this->isDayVisible($day->value, $globalSettings, $typeSettings)) {
                $visibleDays[] = $day->value;
            }
        }

        return $visibleDays;
    }

    /** @param array<int, bool> $globalSettings @param array<int, bool> $typeSettings */
    private function isDayVisible(int $dayOfWeek, array $globalSettings, array $typeSettings): bool
    {
        return $typeSettings[$dayOfWeek] ?? $globalSettings[$dayOfWeek] ?? true;
    }
}
