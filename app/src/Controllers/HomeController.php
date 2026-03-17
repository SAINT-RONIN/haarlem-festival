<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\HomeService;
use App\Services\SessionService;

/**
 * Controller for the homepage.
 *
 * Handles HTTP requests for the main landing page.
 */
class HomeController extends BaseController
{
    public function __construct(
        private HomeService $homeService,
        private SessionService $sessionService,
    ) {
    }

    /**
     * Displays the homepage.
     *
     * GET /
     */
    public function index(): void
    {
        try {
            $viewModel = $this->homeService->getHomePageData($this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/home.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
