<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DanceService;

class DanceController
{
    public function __construct(private DanceService $service = new DanceService()) {}

    public function index(): void
    {
        $viewModel = $this->service->getDancePageData();
        require __DIR__ . '/../Views/pages/dance.php';
    }

    public function detail(string $slug): void
    {
        $viewModel = $this->service->getArtistDetailBySlug($slug);

        if ($viewModel === null) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
            return;
        }

        require __DIR__ . '/../Views/pages/dance-artist-detail.php';
    }
}