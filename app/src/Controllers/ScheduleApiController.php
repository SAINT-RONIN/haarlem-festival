<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\JazzPageConstants;
use App\Constants\ScheduleConstants;
use App\Constants\StorytellingPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\SchedulePageNotFoundException;
use App\Mappers\ScheduleMapper;
use App\DTOs\Schedule\ScheduleRouteConfig;
use App\Services\Interfaces\IScheduleService;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Returns server-rendered schedule HTML fragments for AJAX-driven filter updates
 * on event listing pages (Jazz, Storytelling).
 */
class ScheduleApiController extends BaseController
{
    public function __construct(
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * Returns the rendered schedule section HTML for AJAX filter requests.
     * GET /api/schedule/{pageSlug}
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
        } catch (\Throwable) {
            http_response_code(500);
            echo '';
        }
    }

    /** Builds the schedule view model by resolving page-specific config and applying query-string filters. */
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
     * Maps a URL slug to event-type-specific configuration. New event types require a new case here.
     * @throws SchedulePageNotFoundException if the slug doesn't match any known event page
     */
    private function resolveConfig(string $pageSlug): ScheduleRouteConfig
    {
        return match ($pageSlug) {
            'storytelling' => new ScheduleRouteConfig(StorytellingPageConstants::PAGE_SLUG, EventTypeId::Storytelling->value, ScheduleConstants::MAX_DAYS),
            'jazz' => new ScheduleRouteConfig(JazzPageConstants::PAGE_SLUG, EventTypeId::Jazz->value, ScheduleConstants::MAX_DAYS),
            default => throw new SchedulePageNotFoundException($pageSlug),
        };
    }
}
