<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Mappers\RestaurantViewMapper;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\IRestaurantDetailService;
use App\Services\Interfaces\IRestaurantReservationService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ISessionService;

class RestaurantController extends BaseController
{
    public function __construct(
        private readonly IRestaurantService $restaurantService,
        private readonly IRestaurantDetailService $restaurantDetailService,
        private readonly IRestaurantReservationService $restaurantReservationService,
        private readonly IProgramService $programService,
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
            $data = $this->restaurantDetailService->getDetailPageData($slug);
            $viewModel = RestaurantViewMapper::toDetailViewModel($data, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-detail.php', $viewModel);
        });
    }

    public function reservationPage(string $slug): void
    {
        $this->handlePageRequest(function () use ($slug): void {
            $data = $this->restaurantDetailService->getDetailPageData($slug);
            $viewModel = RestaurantViewMapper::toReservationViewModel($data, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/restaurant-reservation.php', $viewModel);
        });
    }

    public function submitReservation(string $slug): void
    {
        $this->handleJsonRequest(function () use ($slug): void {
            // We need the current session or user so the reservation can be added to the right program.
            $sessionContext = $this->resolveSessionContext();
            $formData = ReservationFormData::fromArray($_POST);
            $result = $this->restaurantReservationService->submitReservation($slug, $formData);

            // The reservation is saved first, then linked to the visitor's program as a separate step.
            $this->programService->addReservationToProgram(
                $sessionContext->sessionKey,
                $sessionContext->userId,
                $result->reservationId,
            );

            $this->json(['success' => true, 'redirect' => '/my-program']);
        }, [ValidationException::class]);
    }
}
