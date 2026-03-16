<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\JazzArtistDetailService;
use App\Services\JazzService;
use App\ViewModels\Jazz\JazzArtistDetailPageViewModel;
use App\ViewModels\Jazz\JazzPageViewModel;

/**
 * Controller for Jazz page.
 */
class JazzController extends BaseController
{
    public function __construct(
        private readonly JazzService $jazzService,
        private readonly JazzArtistDetailService $jazzArtistDetailService,
    ) {
    }

    /**
     * Display the Jazz page.
     */
    public function index(): void
    {
        try {
            $data = $this->jazzService->getJazzPageData();
            $viewModel = JazzPageViewModel::fromData($data);
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Display a Jazz artist detail page by slug.
     */
    public function detail(string $slug): void
    {
        try {
            $data = $this->jazzArtistDetailService->getArtistPageDataBySlug($slug);
            $viewModel = JazzArtistDetailPageViewModel::fromData($data);
            $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error, 404);
        }
    }
}
