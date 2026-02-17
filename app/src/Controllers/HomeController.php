<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
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
        try {
            $homeService = new HomeService();
            $viewModel = $homeService->getHomePageData();
            require __DIR__ . '/../Views/pages/home.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
