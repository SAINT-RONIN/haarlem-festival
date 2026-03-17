<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IHistoryService;
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
        private IHistoryService $historyService,
        private SessionService $sessionService,
    ) {
    }

    public function index(): void
    {
        try {
            $viewModel = $this->historyService->getHistoryPageData($this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/history.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
