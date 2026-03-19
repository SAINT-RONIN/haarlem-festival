<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\JazzPageConstants;
use App\Constants\StorytellingPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\SchedulePageNotFoundException;
use App\Mappers\ScheduleMapper;
use App\Models\ScheduleRouteConfig;
use App\Services\Interfaces\IScheduleService;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class ScheduleApiController extends BaseController
{
    public function __construct(
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * Returns the rendered schedule section HTML for AJAX filter requests.
     */
    public function getScheduleHtml(string $pageSlug): void
    {
        try {
            $scheduleSection = $this->buildScheduleViewModel($pageSlug);
            $scheduleAjaxRender = true;
            header('Content-Type: text/html; charset=utf-8');
            require __DIR__ . '/../Views/partials/sections/schedule/schedule-section.php';
        } catch (SchedulePageNotFoundException) {
            http_response_code(404);
            echo '';
        }
    }

    private function buildScheduleViewModel(string $pageSlug): ScheduleSectionViewModel
    {
        $config = $this->resolveConfig($pageSlug);
        $filterParams = $this->readScheduleFilterParams();
        $scheduleData = $this->scheduleService->getScheduleData(
            $config->pageSlug,
            $config->eventTypeId,
            $config->maxDays,
            filterParams: $filterParams,
        );
        return ScheduleMapper::toScheduleSection($scheduleData);
    }

    /**
     * @throws SchedulePageNotFoundException
     */
    private function resolveConfig(string $pageSlug): ScheduleRouteConfig
    {
        return match ($pageSlug) {
            'storytelling' => new ScheduleRouteConfig(StorytellingPageConstants::PAGE_SLUG, EventTypeId::Storytelling->value, StorytellingPageConstants::SCHEDULE_MAX_DAYS),
            'jazz' => new ScheduleRouteConfig(JazzPageConstants::PAGE_SLUG, EventTypeId::Jazz->value, JazzPageConstants::SCHEDULE_MAX_DAYS),
            default => throw new SchedulePageNotFoundException($pageSlug),
        };
    }
}
