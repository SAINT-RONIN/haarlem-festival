<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\HistoryPageConstants;
use App\Constants\ScheduleConstants;
use App\Enums\EventTypeId;
use App\Mappers\HistoricalLocationMapper;
use App\Mappers\HistoryMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\IHistoricalLocationService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;

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
        ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * GET /history/
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $this->renderIndex();
        });
    }

    private function renderIndex(): void
    {
        $data = $this->historyService->getHistoryPageData();
        $scheduleData = $this->scheduleService->getScheduleData(
            HistoryPageConstants::PAGE_SLUG,
            EventTypeId::History->value,
            ScheduleConstants::MAX_DAYS,
        );
        $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
        $viewModel = HistoryMapper::toPageViewModel($data, $this->isLoggedIn(), $scheduleSection);
        $this->renderPage(__DIR__ . '/../Views/pages/history.php', $viewModel);
    }

    /**
     * Displays a historical location page.
     *
     * GET /history/{id}
     */
    public function location(string $name): void
    {
        $this->handlePageRequest(function () use ($name): void {
            $this->renderLocation($name);
        });
    }

    private function renderLocation(string $name): void
    {
        $data = $this->historicalLocationService->getHistoralLocationPageData($name);
        $viewModel = HistoricalLocationMapper::toPageViewModel($data, $this->isLoggedIn());
        $this->renderPage(__DIR__ . '/../Views/pages/historical-location.php', $viewModel);
    }
}
