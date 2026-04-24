<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\Exceptions\ValidationException;
use App\Mappers\RestaurantViewMapper;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant pages: listing, detail, and reservation.
 */
class RestaurantController extends BaseController
{
    public function __construct(
        private IRestaurantService $restaurantService,
        private IProgramService $programService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the restaurant listing page.
     *
     * GET /restaurant
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->restaurantService->getRestaurantPageData();
            $cuisines = $this->restaurantService->getActiveCuisines($data->restaurants);
            $viewModel = RestaurantViewMapper::toPageViewModel($data, $cuisines, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        });
    }

    /**
     * Displays a single restaurant detail page by slug.
     *
     * GET /restaurant/{slug}
     */
    public function detail(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $restaurant = $this->restaurantService->getRestaurant($slug);
            $viewModel = RestaurantViewMapper::toDetailViewModel($restaurant, $this->restaurantService, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        });
    }

    /**
     * Displays the reservation form for a restaurant.
     *
     * GET /restaurant/{slug}/reservation
     */
    public function reservationPage(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $restaurant = $this->restaurantService->getRestaurant($slug);
            $viewModel = RestaurantViewMapper::toDetailViewModel($restaurant, $this->restaurantService, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        });
    }

    /**
     * Processes a reservation form submission.
     *
     * POST /restaurant/{slug}/reservation
     */
    public function submitReservation(string $slug): void
    {
        $this->handleJsonRequest(function () use ($slug): void {
            $sessionContext = $this->resolveSessionContext();
            $formData = ReservationFormData::fromArray($_POST);
            $reservationId = $this->restaurantService->submitReservation($slug, $formData);

            $this->programService->addReservationToProgram(
                $sessionContext->sessionKey,
                $sessionContext->userId,
                $reservationId,
            );

            $this->json(['success' => true, 'redirect' => '/my-program']);
        }, [ValidationException::class]);
    }
}
