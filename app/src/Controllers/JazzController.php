<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\JazzArtistDetailConstants;
use App\Constants\JazzPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Mappers\JazzMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\IJazzArtistDetailService;
use App\Services\Interfaces\IJazzService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\Controllers\Support\ControllerErrorResponder;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Serves the Jazz event listing page and individual artist detail pages,
 * including schedule sections with day/time/venue filtering.
 */
class JazzController extends BaseController
{
    public function __construct(
        private readonly IJazzService $jazzService,
        private readonly IJazzArtistDetailService $jazzArtistDetailService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * Renders the main Jazz listing page with all artists and a filterable schedule.
     * GET /jazz
     */
    public function index(): void
    {
        try {
            $data = $this->jazzService->getJazzPageData();
            $scheduleSection = $this->buildListingScheduleSection();
            $viewModel = JazzMapper::toPageViewModel($data, $scheduleSection, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function buildListingScheduleSection(): ScheduleSectionViewModel
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            JazzPageConstants::PAGE_SLUG,
            EventTypeId::Jazz->value,
            JazzPageConstants::SCHEDULE_MAX_DAYS,
            filterParams: $this->readScheduleFilterParams(),
        );
        return ScheduleMapper::toScheduleSection($scheduleData);
    }

    /**
     * Renders the detail page for a single Jazz artist, identified by URL slug. Returns 404 if not found.
     * GET /jazz/{slug}
     */
    public function detail(string $slug): void
    {
        try {
            $this->renderDetailPage($slug);
        } catch (JazzArtistDetailNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function renderDetailPage(string $slug): void
    {
        $pageData = $this->jazzArtistDetailService->getArtistPageDataBySlug($slug);
        $scheduleData = $this->scheduleService->getScheduleData(
            JazzArtistDetailConstants::SCHEDULE_PAGE_SLUG,
            EventTypeId::Jazz->value,
            JazzArtistDetailConstants::SCHEDULE_MAX_DAYS,
            $pageData->eventId,
        );
        $performances = ScheduleMapper::flattenEventsAsViewModels($scheduleData);
        $viewModel = JazzMapper::toArtistDetailViewModel($pageData, $performances);
        $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
    }

}
