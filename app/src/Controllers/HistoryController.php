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
class HistoryController
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

    public function index(): void
    {
        try {
            $viewModel = $this->historyService->getHistoryPageData();
            require __DIR__ . '/../Views/pages/history.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
