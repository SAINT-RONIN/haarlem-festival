<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\DanceMapper;
use App\Services\DanceService;

class DanceController
{
    public function __construct(private DanceService $service = new DanceService())
    {
    }

    public function index(): void
    {
        $viewModel = DanceMapper::toPageViewModel($this->service->getPageData());
        require __DIR__ . '/../Views/pages/dance.php';
    }

    public function detail(string $slug): void
    {
        $artist = $this->service->getArtistDetailBySlug($slug);

        if ($artist === null) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
            return;
        }

        $viewModel = DanceMapper::toDetailViewModel($artist);
        require __DIR__ . '/../Views/pages/dance-artist-detail.php';
    }
}