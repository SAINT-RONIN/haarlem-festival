<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DanceService;
use App\Services\Interfaces\ISessionService;

class DanceController extends BaseController
{
    public function __construct(
        private readonly DanceService $danceService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $viewModel = $this->danceService->getDancePageData($this->isLoggedIn());
            $this->renderView(__DIR__ . '/../Views/pages/dance.php', $viewModel);
        });
    }
}
