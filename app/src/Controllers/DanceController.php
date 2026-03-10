<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DanceService;

class DanceController
{
    private DanceService $danceService;

    public function __construct()
    {
        $this->danceService = new DanceService();
    }
//
    public function index(): void
    {
        $viewModel = $this->danceService->getDancePageData();
        require __DIR__ . '/../Views/pages/dance.php';
    }
}