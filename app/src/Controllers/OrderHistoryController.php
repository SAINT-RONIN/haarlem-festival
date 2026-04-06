<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\OrderHistoryMapper;
use App\Services\Interfaces\IOrderHistoryService;
use App\Services\Interfaces\ISessionService;

/**
 * Displays the customer's past orders and provides access to ticket PDF downloads.
 * Requires authentication — unauthenticated visitors are redirected to the login page.
 */
class OrderHistoryController extends BaseController
{
    public function __construct(
        private readonly IOrderHistoryService $orderHistoryService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Renders the "My Orders" page with order history and ticket download links.
     * GET /my-orders
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $this->renderOrderHistoryPage();
        });
    }

    /** Validates login, fetches order data, and renders the order history view. */
    private function renderOrderHistoryPage(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirectAndExit('/login');
        }

        $userId = $this->resolveSessionContext()->userId;
        $orderHistoryData = $this->orderHistoryService->getOrderHistoryData($userId);

        $viewModel = OrderHistoryMapper::toMyOrdersViewModel(
            $orderHistoryData['orders'],
            $orderHistoryData['ticketsByOrder'],
            true,
        );

        $this->renderView(__DIR__ . '/../Views/pages/my-orders.php', $viewModel);
    }
}
