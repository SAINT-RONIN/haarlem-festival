<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\GumboKingsDetailService;
use App\Services\JazzService;

/**
 * Controller for Jazz page.
 */
class JazzController
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
        $viewModel = $this->jazzService->getJazzPageData();
        require __DIR__ . '/../Views/pages/jazz.php';
    }

    /**
     * Display Gumbo Kings artist detail page.
     */
    public function gumboKings(): void
    {
        $viewModel = $this->gumboKingsDetailService->getPageData();
        require __DIR__ . '/../Views/pages/jazz-gumbo-kings.php';
    }
}
