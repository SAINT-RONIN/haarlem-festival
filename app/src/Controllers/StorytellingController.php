<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\StorytellingService;

/**
 * Controller for the storytelling page.
 *
 * Handles HTTP requests for the storytelling landing page.
 */
class StorytellingController extends BaseController
{
    /**
     * Displays the storytelling page.
     *
     * GET /storytelling
     */
    public function index(): void
    {
        try {
            $storytellingService = new StorytellingService();
            $viewModel = $storytellingService->getStorytellingPageData();
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
