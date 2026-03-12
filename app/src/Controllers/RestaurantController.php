<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\Restaurant\RestaurantViewModelMapper;
use App\Services\Interfaces\IRestaurantService;
use App\Services\RestaurantService;

/**
 * Controller for Restaurant pages.
 *
 * Thin controller — only orchestrates:
 *   1. Calls the SERVICE for plain business data.
 *   2. Calls the MAPPER to convert data into ViewModels.
 *   3. Renders the view.
 *   4. Handles errors / 404.
 *
 * Dependencies are received through the constructor.
 * When called with no arguments (as the router does: new RestaurantController()),
 * sensible defaults are created automatically.
 */
class RestaurantController extends BaseController
{
    private IRestaurantService $restaurantService;
    private RestaurantViewModelMapper $mapper;

    /**
     * @param IRestaurantService|null        $restaurantService  Defaults to RestaurantService.
     * @param RestaurantViewModelMapper|null  $mapper             Defaults to a new mapper instance.
     */
    public function __construct(
        ?IRestaurantService $restaurantService = null,
        ?RestaurantViewModelMapper $mapper = null
    ) {
        $this->restaurantService = $restaurantService ?? new RestaurantService();
        $this->mapper            = $mapper ?? new RestaurantViewModelMapper();
    }

    /**
     * Displays the restaurant listing page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        try {
            $data      = $this->restaurantService->getRestaurantPageData();
            $viewModel = $this->mapper->toPageViewModel($data);

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

            $viewModel = $this->mapper->toDetailViewModel($data);

            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
