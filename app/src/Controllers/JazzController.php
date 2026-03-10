<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\GumboKingsDetailService;
use App\Services\JazzService;

/**
 * Controller for Jazz page.
 */
class JazzController extends BaseController
{
    private JazzService $jazzService;
    private GumboKingsDetailService $gumboKingsDetailService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
        $this->gumboKingsDetailService = new GumboKingsDetailService();
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
            $viewModel = $this->gumboKingsDetailService->getPageData();
            require __DIR__ . '/../Views/pages/jazz-gumbo-kings.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
