<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\GlobalUiConstants;
use App\Constants\JazzArtistDetailConstants;
use App\Constants\JazzPageConstants;
use App\Constants\ScheduleConstants;
use App\Enums\EventTypeId;
use App\Mappers\JazzMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\IJazzArtistDetailService;
use App\Services\Interfaces\IJazzService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Jazz listing page and artist detail pages with filterable schedule.
 */
class JazzController extends BaseController
{
    public function __construct(
        private readonly IJazzService $jazzService,
        private readonly IJazzArtistDetailService $jazzArtistDetailService,
        ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->jazzService->getJazzPageData();
            $scheduleSection = $this->buildListingScheduleSection();
            $viewModel = JazzMapper::toPageViewModel($data, $scheduleSection, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        });
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

    public function detail(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $this->renderDetailPage($slug);
        });
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
        $appUrl = (string) (getenv('APP_URL') ?: GlobalUiConstants::DEFAULT_APP_URL);
        $viewModel = JazzMapper::toArtistDetailViewModel($pageData, $performances, $currentUri, $appUrl);
        $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
    }

}
