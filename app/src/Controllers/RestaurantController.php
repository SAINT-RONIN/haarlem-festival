<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\RestaurantViewMapper;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant page.
 */
class RestaurantController extends BaseController
{
    public function __construct(
        private readonly IRestaurantService $restaurantService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the restaurant page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->restaurantService->getRestaurantPageData();
            $viewModel = RestaurantViewMapper::toPageViewModel($data, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        });
    }

    /**
     * Displays a single restaurant detail page.
     *
     * GET /restaurant/{id}
     */
    public function detail(string $id): void
    {
        $this->handlePageRequest(function () use ($id): void {
            $this->renderRestaurantDetail($id);
        });
    }

    /** Loads restaurant data, returns 404 if not found, otherwise builds VM and renders detail page. */
    private function renderRestaurantDetail(string $id): void
    {
        $data = $this->restaurantService->getRestaurantDetailData((int) $id);

        if ($data === null) {
            $this->renderNotFoundPage();
            return;
        }

        $viewModel = RestaurantViewMapper::toDetailViewModel($data, $this->isLoggedIn());
        $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
    }
}
