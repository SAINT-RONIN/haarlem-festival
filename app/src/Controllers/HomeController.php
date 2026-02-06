<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\HomeService;

/**
 * Controller for the homepage.
 *
 * Handles HTTP requests for the main landing page.
 */
class HomeController
{
    /**
     * Displays the homepage.
     *
     * GET /
     */
    public function index(): void
    {
        $homeService = new HomeService();
        $viewModel = $homeService->getHomePageData();

        // Pass viewModel to the view
        require __DIR__ . '/../Views/pages/home.php';
    }
}

