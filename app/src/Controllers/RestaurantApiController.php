<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\RestaurantMapper;
use App\Services\Interfaces\IRestaurantService;

/**
 * Returns filtered restaurant cards HTML for AJAX filter requests.
 */
class RestaurantApiController extends BaseController
{
    public function __construct(
        private readonly IRestaurantService $restaurantService,
    ) {
    }

    /**
     * GET /api/restaurants?cuisine={value}
     */
    public function getCardsHtml(): void
    {
        $cuisineFilter = trim($_GET['cuisine'] ?? '');

        $data                    = $this->restaurantService->getRestaurantPageData($cuisineFilter ?: null);
        $viewModel               = RestaurantMapper::toPageViewModel($data, false);
        $restaurantCardsSection  = $viewModel->restaurantCardsSection;

        header('Content-Type: text/html; charset=utf-8');
        require __DIR__ . '/../Views/partials/restaurant/restaurant-cards-section.php';
    }
}
