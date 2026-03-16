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
    private JazzService $jazzService;
    private JazzArtistDetailService $jazzArtistDetailService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
        $this->jazzArtistDetailService = new JazzArtistDetailService();
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
            require __DIR__ . '/../Views/pages/jazz-artist-detail.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error, 404);
        }
    }
}
