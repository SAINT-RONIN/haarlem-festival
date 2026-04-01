<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\RestaurantViewMapper;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Returns filtered restaurant cards HTML for AJAX filter requests.
 */
class RestaurantApiController extends BaseController
{
    public function __construct(
        private readonly IRestaurantService $restaurantService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * GET /api/restaurants?cuisine={value}
     */
    public function getCardsHtml(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->restaurantService->getRestaurantPageData();
            $viewModel = RestaurantViewMapper::toPageViewModel($data, $this->isLoggedIn());
            $restaurantCardsSection = $viewModel->restaurantCardsSection;

            header('Content-Type: text/html; charset=utf-8');
            require __DIR__ . '/../Views/partials/restaurant/restaurant-cards-section.php';
        });
    }
}
