<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\RestaurantPageConstants;
use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\RestaurantMapper;
use App\Repositories\ReservationRepository;
use App\Models\Reservation;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Restaurant pages.
 */
class RestaurantController extends BaseController
{
    public function __construct(
        private IRestaurantService    $restaurantService,
        private ISessionService       $sessionService,
        private ReservationRepository $reservationRepository,
    ) {
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
            $viewModel = RestaurantMapper::toPageViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays a single restaurant detail page.
     *
     * GET /restaurant/{slug}
     */
    public function detail(string $slug): void
    {
        try {
            $data = $this->restaurantService->getRestaurantDetailData($slug);

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

    /**
     * Displays the reservation form for a restaurant.
     *
     * GET /restaurant/{slug}/reservation
     */
    public function reservationPage(string $slug): void
    {
        try {
            $data = $this->restaurantService->getRestaurantDetailData($slug);

            if ($data === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $viewModel = RestaurantMapper::toReservationViewModel($data, $this->sessionService->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Handles reservation form submission.
     *
     * POST /restaurant/{slug}/reservation
     */
    public function submitReservation(string $slug): void
    {
        try {
            $data = $this->restaurantService->getRestaurantDetailData($slug);

            if ($data === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $date            = trim((string) ($_POST['dining_date']      ?? ''));
            $timeSlot        = trim((string) ($_POST['time_slot']        ?? ''));
            $adultsCount     = max(0, (int) ($_POST['adults_count']      ?? 0));
            $childrenCount   = max(0, (int) ($_POST['children_count']    ?? 0));
            $specialRequests = trim((string) ($_POST['special_requests'] ?? ''));

            $errors = [];

            if (!in_array($date, RestaurantPageConstants::VALID_DATES, true)) {
                $errors[] = 'Please select a valid dining date.';
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
                header("Location: /restaurant/{$slug}/reservation");
                exit;
            }

            $totalFee = ($adultsCount + $childrenCount) * RestaurantPageConstants::RESERVATION_FEE;

            $reservation = new Reservation(
                restaurantId:    $data->event->restaurantId,
                diningDate:      $date,
                timeSlot:        $timeSlot,
                adultsCount:     $adultsCount,
                childrenCount:   $childrenCount,
                specialRequests: $specialRequests,
                totalFee:        $totalFee,
            );

            $this->reservationRepository->insert($reservation);

            header("Location: /restaurant/{$slug}/reservation?success=1");
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
