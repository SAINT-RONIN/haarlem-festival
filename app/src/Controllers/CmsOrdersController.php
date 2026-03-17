<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\CmsOrdersService;
use App\ViewModels\Cms\CmsOrderListItemViewModel;
use App\ViewModels\Cms\CmsOrdersListViewModel;

class CmsOrdersController
{
    private CmsOrdersService $ordersService;

    public function __construct()
    {
        $this->ordersService = new CmsOrdersService();
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin();

            $currentView  = 'orders';
            $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

            $ordersData = $this->ordersService->getOrdersWithDetails($statusFilter);

            $orders = array_map(
                static fn(array $row): CmsOrderListItemViewModel => CmsOrderListItemViewModel::fromRow($row),
                $ordersData
            );

            $viewModel = new CmsOrdersListViewModel(
                orders:         $orders,
                selectedStatus: $_GET['status'] ?? '',
                successMessage: $_GET['success'] ?? null,
                errorMessage:   $_GET['error'] ?? null,
            );

            require __DIR__ . '/../Views/pages/cms/orders.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
