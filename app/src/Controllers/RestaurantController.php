<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IRestaurantService;
use App\Services\RestaurantService;

/**
 * Controller for Restaurant page.
 */
class RestaurantController extends BaseController
{
    private IRestaurantService $restaurantService;

    public function __construct()
    {
        $this->restaurantService = new RestaurantService();
    }

    /**
     * Displays the restaurant page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        try {
            $viewModel = $this->restaurantService->getRestaurantPageData();
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
