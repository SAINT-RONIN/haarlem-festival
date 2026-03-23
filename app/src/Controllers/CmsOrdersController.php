<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsOrdersMapper;
use App\Services\Interfaces\ICmsOrdersService;
use App\Services\Interfaces\ISessionService;

class CmsOrdersController
{
    public function __construct(
        private readonly ICmsOrdersService $ordersService,
        private readonly ISessionService $sessionService,
    ) {
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView  = 'orders';
            $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

            $ordersData = $this->ordersService->getOrdersWithDetails($statusFilter);

            $viewModel = CmsOrdersMapper::toListViewModel(
                $ordersData,
                $_GET['status'] ?? '',
                $this->sessionService->consumeFlash('success'),
                $this->sessionService->consumeFlash('error'),
            );

            require __DIR__ . '/../Views/pages/cms/orders.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
