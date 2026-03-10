<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IStorytellingService;
use App\Services\StorytellingService;

/**
 * Controller for the storytelling page.
 *
 * Handles HTTP requests for the storytelling landing page.
 */
class StorytellingController extends BaseController
{
    private IStorytellingService $storytellingService;

    public function __construct()
    {
        $this->storytellingService = new StorytellingService();
    }

    /**
     * Displays the storytelling page.
     *
     * GET /storytelling
     */
    public function index(): void
    {
        try {
            $viewModel = $this->storytellingService->getStorytellingPageData();
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays the storytelling detail page for a single event.
     *
     * GET /storytelling/{id}
     */
    public function detail(string $id): void
    {
        $eventId = (int)$id;

        $storytellingService = new StorytellingService();

        try {
            $viewModel = $storytellingService->getStorytellingDetailPageData($eventId);
        } catch (\RuntimeException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
            return;
        }

        require __DIR__ . '/../Views/pages/storytelling-detail.php';
    }
}
