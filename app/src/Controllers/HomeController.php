<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\HomeMapper;
use App\Services\Interfaces\IHomeService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for the homepage.
 *
 * Handles HTTP requests for the main landing page.
 */
class HomeController extends BaseController
{
    public function __construct(
        private IHomeService $homeService,
        private ISessionService $sessionService,
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
            $data = $this->homeService->getHomePageData();
            $viewModel = HomeMapper::toPageViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/home.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
