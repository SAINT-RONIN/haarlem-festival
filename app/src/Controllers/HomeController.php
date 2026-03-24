<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\HomeMapper;
use App\Services\Interfaces\IHomeService;
use App\Services\Interfaces\ISessionService;

/**
 * Serves the festival homepage with CMS-driven hero, highlights, and event teasers.
 */
class HomeController extends BaseController
{
    public function __construct(
        private readonly IHomeService $homeService,
        private readonly ISessionService $sessionService,
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
