<?php

declare(strict_types=1);

namespace App\Controllers;

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
        $viewModel = $this->historyService->getHistoryPageData();
        require __DIR__ . '/../Views/pages/history.php';
    }
}
