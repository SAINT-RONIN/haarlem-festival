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
class HomeController extends BaseController
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
            $this->renderPage(__DIR__ . '/../Views/pages/home.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
