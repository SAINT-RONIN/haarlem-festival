<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\JazzService;

/**
 * Controller for Jazz page.
 */
class JazzController
{
    private JazzService $jazzService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
    }

    /**
     * Display the Jazz page.
     */
    public function index(): void
    {
        $viewModel = $this->jazzService->getJazzPageData();
        require __DIR__ . '/../Views/pages/jazz.php';
    }
}
