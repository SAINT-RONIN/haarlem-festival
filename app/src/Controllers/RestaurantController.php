<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\RestaurantMapper;
use App\Repositories\ReservationRepository;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant page.
 */
class RestaurantController extends BaseController
{
    private const RESERVATION_FEE   = 10.00;
    private const VALID_DATES        = ['Thursday', 'Friday', 'Saturday', 'Sunday'];

    public function __construct(
        private IRestaurantService $restaurantService,
        private ISessionService $sessionService,
        private ReservationRepository $reservationRepository = new ReservationRepository(),
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

            $viewModel = RestaurantMapper::toDetailViewModel($data, $this->sessionService->isLoggedIn(), self::VALID_DATES, self::RESERVATION_FEE);
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays the reservation form page.
     *
     * GET /restaurant/{id}/reservation
     */
    public function reservationPage(string $id): void
    {
        try {
            $data = $this->restaurantService->getRestaurantDetailData((int) $id);

            if ($data === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $viewModel = RestaurantMapper::toDetailViewModel($data, $this->sessionService->isLoggedIn(), self::VALID_DATES, self::RESERVATION_FEE);
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Handles reservation form submission.
     *
     * POST /restaurant/{id}/reservation
     */
    public function submitReservation(string $id): void
    {
        $restaurantId = (int) $id;
        $redirectUrl  = '/restaurant/' . $restaurantId . '/reservation';

        $diningDate    = trim($_POST['dining_date'] ?? '');
        $timeSlot      = trim($_POST['time_slot']   ?? '');
        $adultsCount   = (int) ($_POST['adults_count']   ?? 0);
        $childrenCount = (int) ($_POST['children_count'] ?? 0);

        $errors = [];
        if (!in_array($diningDate, self::VALID_DATES, true)) {
            $errors[] = 'Please select a valid date.';
        }
        if ($timeSlot === '') {
            $errors[] = 'Please select a time slot.';
        }
        if ($adultsCount + $childrenCount < 1) {
            $errors[] = 'Please add at least one guest.';
        }

        if ($errors !== []) {
            $_SESSION['reservation_errors']    = $errors;
            $_SESSION['reservation_old_input'] = $_POST;
            header('Location: ' . $redirectUrl);
            return;
        }

        $this->reservationRepository->insert(
            RestaurantMapper::toReservation($_POST, $restaurantId, self::RESERVATION_FEE)
        );

        header('Location: ' . $redirectUrl . '?success=1');
    }
}
