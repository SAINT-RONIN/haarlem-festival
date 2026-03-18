<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\RestaurantMapper;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant page.
 */
class RestaurantController extends BaseController
{
    public function __construct(
        private IRestaurantService $restaurantService,
        private ISessionService $sessionService,
    ) {
    }

    /**
     * Displays the restaurant page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        try {
            $data = $this->restaurantService->getRestaurantPageData();
            $viewModel = RestaurantMapper::toPageViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays a single restaurant detail page.
     *
     * GET /restaurant/{id}
     */
    public function detail(string $id): void
    {
        try {
            $data = $this->restaurantService->getRestaurantDetailData((int) $id);

            if ($data === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $viewModel = RestaurantMapper::toDetailViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
