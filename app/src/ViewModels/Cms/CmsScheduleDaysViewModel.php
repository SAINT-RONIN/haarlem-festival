<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\EventType;
use App\Models\ScheduleDayConfig;

/**
 * ViewModel for the CMS schedule days management page.
 */
final readonly class CmsScheduleDaysViewModel
{
    /**
     * @param EventType[] $eventTypes
     * @param array<int, ScheduleDayConfig> $globalConfigs Day configs without an event type
     * @param array<int, array<int, ScheduleDayConfig>> $typeConfigs Day configs keyed by event type ID then day
     */
    public function __construct(
        public array   $eventTypes,
        public array   $globalConfigs,
        public array   $typeConfigs,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {
    }
}
