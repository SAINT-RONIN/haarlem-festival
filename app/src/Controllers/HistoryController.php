<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\HistoryPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\Mappers\HistoricalLocationMapper;
use App\Mappers\HistoryMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\IHistoricalLocationService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Controller for the history page.
 *
 * Handles HTTP requests for the history landing page.
 */
class HistoryController extends BaseController
{
    public function __construct(
        private readonly IHistoryService $historyService,
        private readonly IHistoricalLocationService $historicalLocationService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * GET /history/
     */
    public function index(): void
    {
        $data = $this->historyService->getHistoryPageData();
        $scheduleData = $this->scheduleService->getScheduleData(
            HistoryPageConstants::PAGE_SLUG,
            EventTypeId::History->value,
            HistoryPageConstants::SCHEDULE_MAX_DAYS,
            filterParams: $this->readScheduleFilterParams(),
        );
        $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
        $viewModel = HistoryMapper::toPageViewModel($data, $this->sessionService->isLoggedIn(), $scheduleSection);
        $this->renderPage(__DIR__ . '/../Views/pages/history.php', $viewModel);
    }

    /**
     * Displays a historical location page.
     *
     * GET /history/{id}
     */
    public function location(string $name): void
    {
        try {
            $this->renderLocation($name);
        } catch (HistoricalLocationNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        }
    }

    private function renderLocation(string $name): void
    {
        $data = $this->historicalLocationService->getHistoralLocationPageData($name);
        $viewModel = HistoricalLocationMapper::toPageViewModel($data, $this->sessionService->isLoggedIn());
        $this->renderPage(__DIR__ . '/../Views/pages/historical-location.php', $viewModel);
    }
}
