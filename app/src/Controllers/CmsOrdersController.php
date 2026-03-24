<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsOrdersMapper;
use App\Services\Interfaces\ICmsOrdersService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for viewing customer orders.
 *
 * Provides a read-only list of orders with optional payment-status filtering
 * for admin review and support purposes. Intentionally read-only: order
 * mutations (payment confirmation, refunds) happen through the payment
 * provider webhooks, not through this controller.
 */
class CmsOrdersController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsOrdersService $ordersService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the orders list with optional payment-status filtering.
     * GET /cms/orders
     */
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
