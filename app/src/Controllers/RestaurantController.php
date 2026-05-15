<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\Exceptions\ValidationException;
use App\Mappers\RestaurantViewMapper;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

class RestaurantController extends BaseController
{
    public function __construct(
        private IRestaurantService $restaurantService,
        private IProgramService $programService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data = $this->restaurantService->getRestaurantPageData();
            $viewModel = RestaurantViewMapper::toPageViewModel($data, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant.php', $viewModel);
        });
    }

    public function detail(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $viewModel = $this->buildDetailViewModel($slug);
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        });
    }

    public function reservationPage(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $viewModel = $this->buildDetailViewModel($slug);
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        });
    }

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

    private function buildDetailViewModel(string $slug): \App\ViewModels\Restaurant\RestaurantDetailViewModel
    {
        $data = $this->restaurantService->getDetailPageData($slug);

        return RestaurantViewMapper::toDetailViewModel($data->restaurant, $data->detailLabels, $data->globalUi, $this->isLoggedIn(), $data->validDates);
    }
}