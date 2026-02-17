<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\RestaurantPageService;

/**
 * Controller for the restaurant page.
 *
 * Handles HTTP requests for the restaurant landing page.
 */
class RestaurantController
{
    /**
     * Displays the restaurant page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        try {
            $service = new RestaurantPageService();
            $viewModel = $service->getRestaurantPageData();
            require __DIR__ . '/../Views/pages/restaurant.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
