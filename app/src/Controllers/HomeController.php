<?php

declare(strict_types=1);

namespace App\Controllers;

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
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->homeService->getHomePageData();
            $viewModel = HomeMapper::toPageViewModel($data, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/home.php', $viewModel);
        });
    }
}
