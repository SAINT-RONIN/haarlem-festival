<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\JazzService;

/**
 * Controller for Jazz page.
 */
class JazzController extends BaseController
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
        try {
            $viewModel = $this->jazzService->getJazzPageData();
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
