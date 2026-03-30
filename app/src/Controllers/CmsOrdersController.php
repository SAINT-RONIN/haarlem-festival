<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;
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
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'orders';
            $statusFilter = $this->readStringQueryParam('status');
            $viewModel = $this->buildOrdersViewModel($statusFilter);
            require __DIR__ . '/../Views/pages/cms/orders.php';
        });
    }

    /**
     * Displays a single order's full detail page.
     * GET /cms/orders/{id}
     */
    public function detail(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $currentView = 'orders';
            $viewModel = $this->buildOrderDetailViewModel($id);
            require __DIR__ . '/../Views/pages/cms/order-detail.php';
        });
    }

    /** Fetches orders from the service and maps them to the list view model. */
    private function buildOrdersViewModel(?string $statusFilter): \App\ViewModels\Cms\CmsOrdersListViewModel
    {
        $ordersData = $this->ordersService->getOrdersWithDetails($statusFilter);

        return CmsOrdersMapper::toListViewModel(
            $ordersData,
            $statusFilter ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }

    /** Fetches a single order's detail data and maps it to the detail view model. */
    private function buildOrderDetailViewModel(int $orderId): \App\ViewModels\Cms\CmsOrderDetailViewModel
    {
        $data = $this->ordersService->getOrderDetail($orderId);
        if ($data === null) {
            throw new NotFoundException("Order #{$orderId} not found.");
        }

        return CmsOrdersMapper::toDetailViewModel(
            $data,
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }
}
