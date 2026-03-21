<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\HistoryPageConstants;
use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\EventTypeId;
use App\Mappers\CmsMapper;
use App\Mappers\HistoricalLocationMapper;
use App\Mappers\HistoryMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IHistoricalLocationService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\Services\SessionService;

/**
 * Controller for the history page.
 *
 * Handles HTTP requests for the history landing page.
 */
class HistoryController extends BaseController
{
    /**
     * Displays the history page.
     *
     * GET /history
     */
    public function __construct(
        private readonly IHistoryService $historyService,
        private readonly IHistoricalLocationService $historicalLocationService,
        private readonly ICmsPageContentService $cmsService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * GET /history/
     */
//    public function index(): void
//    {
//        try {
//            $viewModel = $this->historyService->getHistoryPageData($this->sessionService->isLoggedIn());
//            $this->renderPage(__DIR__ . '/../Views/pages/history.php', $viewModel);
//        } catch (\Throwable $error) {
//            ControllerErrorResponder::respond($error);
//        }
//    }

    public function index(): void
    {
        try {
            $data = $this->historyService->getHistoryPageData();
            $scheduleData = $this->scheduleService->getScheduleData(
                HistoryPageConstants::PAGE_SLUG,
                EventTypeId::History->value,
                HistoryPageConstants::SCHEDULE_MAX_DAYS,
            );
            $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
            $viewModel = HistoryMapper::toPageViewModel($data, $this->sessionService->isLoggedIn(), $scheduleSection);
            $this->renderPage(__DIR__ . '/../Views/pages/history.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays a historical location page.
     *
     * GET /history/{id}
     */
    public function location(string $name): void
    {
        try {
            $data = $this->historicalLocationService->getHistoralLocationPageData($name);

            $viewModel = HistoricalLocationMapper::toPageViewModel(
                $data,
                $this->sessionService->isLoggedIn(),
            );

            $this->renderPage(__DIR__ . '/../Views/pages/historical-location.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
