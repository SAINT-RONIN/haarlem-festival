<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\JazzArtistDetailService;
use App\Services\JazzService;

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
            $viewModel = $this->jazzService->getJazzPageData();
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Display Gumbo Kings artist detail page.
     */
    public function gumboKings(): void
    {
        try {
            $viewModel = $this->jazzArtistDetailService->getGumboKingsPageData();
            require __DIR__ . '/../Views/pages/jazz-artist-detail.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Display Ntjam Rosie artist detail page.
     */
    public function ntjamRosie(): void
    {
        try {
            $viewModel = $this->jazzArtistDetailService->getNtjamRosiePageData();
            require __DIR__ . '/../Views/pages/jazz-artist-detail.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
