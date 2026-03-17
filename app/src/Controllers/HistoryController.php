<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\HistoryService;
use App\Services\Interfaces\IHistoryService;

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
    private IHistoryService $historyService;

    public function __construct()
    {
        $this->historyService = new HistoryService();
    }

    /**
     * GET /history/
     */
    public function index(): void
    {
        try {
            $viewModel = $this->historyService->getHistoryPageData();
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
            $viewModel = $this->historyService->getHistoralLocationData($name);

            if ($viewModel === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $this->renderPage(__DIR__ . '/../Views/pages/history-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
