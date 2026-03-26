<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\JazzArtistDetailConstants;
use App\Constants\JazzPageConstants;
use App\Constants\ScheduleConstants;
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
 * Public-facing controller for the Jazz festival section.
 *
 * Serves the Jazz event listing page (all artists + filterable schedule)
 * and individual artist detail pages (bio + that artist's performances).
 *
 * Schedule data is fetched via IScheduleService which applies CMS-configured
 * day visibility rules and optional query-string filters (day, time range,
 * price type, venue). The schedule is scoped to EventTypeId::Jazz so only
 * jazz events appear.
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
            ScheduleConstants::MAX_DAYS,
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

    /** Loads artist data by slug and their scheduled performances, then renders the detail view. */
    private function renderDetailPage(string $slug): void
    {
        $pageData = $this->jazzArtistDetailService->getArtistPageDataBySlug($slug);
        // Fetch schedule scoped to this specific artist's event, so only their performances show
        $scheduleData = $this->scheduleService->getScheduleData(
            JazzArtistDetailConstants::SCHEDULE_PAGE_SLUG,
            EventTypeId::Jazz->value,
            ScheduleConstants::MAX_DAYS,
            $pageData->eventId,
        );
        // Flatten day-grouped schedule into a flat list of performance view-models for the detail layout
        $performances = ScheduleMapper::flattenEventsAsViewModels($scheduleData);
        $currentUri = $_SERVER['REQUEST_URI'] ?? '';
        $appUrl = (string)(getenv('APP_URL') ?: 'https://haarlemfestival.nl');
        $viewModel = JazzMapper::toArtistDetailViewModel($pageData, $performances, $currentUri, $appUrl);
        $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
    }

}
