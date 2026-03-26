<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Mappers\RestaurantMapper;
use App\Services\Interfaces\IRestaurantDetailService;
use App\Services\Interfaces\IRestaurantReservationService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant pages.
 */
class RestaurantController extends BaseController
{
    public function __construct(
        private readonly IRestaurantService            $restaurantService,
        private readonly IRestaurantDetailService      $restaurantDetailService,
        private readonly IRestaurantReservationService $restaurantReservationService,
        private readonly ISessionService               $sessionService,
    ) {
    }

    /**
     * GET /restaurant
     */
    public function index(): void
    {
        try {
            $data      = $this->restaurantService->getRestaurantPageData();
            $viewModel = RestaurantMapper::toPageViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * GET /restaurant/{slug}
     */
    public function detail(string $slug): void
    {
        try {
            $data      = $this->restaurantDetailService->getDetailPageData($slug);
            $viewModel = RestaurantMapper::toDetailViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        } catch (RestaurantEventNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * GET /restaurant/{slug}/reservation
     */
    public function reservationPage(string $slug): void
    {
        try {
            $data      = $this->restaurantDetailService->getDetailPageData($slug);
            $viewModel = RestaurantMapper::toReservationViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        } catch (RestaurantEventNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * POST /restaurant/{slug}/reservation
     */
    public function submitReservation(string $slug): void
    {
        try {
            $this->restaurantReservationService->submitReservation($slug, $_POST);
            header("Location: /restaurant/{$slug}/reservation?success=1");
            exit;
        } catch (RestaurantEventNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (ValidationException $e) {
            $_SESSION['reservation_errors']    = $e->getErrors();
            $_SESSION['reservation_old_input'] = $_POST;
            header("Location: /restaurant/{$slug}/reservation");
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
