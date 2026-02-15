<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\StorytellingService;

/**
 * Controller for the storytelling page.
 *
 * Handles HTTP requests for the storytelling landing page.
 */
class StorytellingController
{
    /**
     * Displays the storytelling page.
     *
     * GET /storytelling
     */
    public function index(): void
    {
        $storytellingService = new StorytellingService();
        $viewModel = $storytellingService->getStorytellingPageData();

        require __DIR__ . '/../Views/pages/storytelling.php';
    }
}
