<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\OrderHistory\OrderSummaryDto;
use App\DTOs\OrderHistory\TicketPdfDto;
use App\Mappers\OrderHistoryMapper;
use App\Repositories\Interfaces\IOrderHistoryRepository;
use App\Services\Interfaces\ISessionService;

/**
 * Displays the customer's past orders and provides access to ticket PDF downloads.
 * Requires authentication — unauthenticated visitors are redirected to the login page.
 */
class OrderHistoryController extends BaseController
{
    public function __construct(
        private readonly IOrderHistoryRepository $orderHistoryRepository,
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
        $orderRows = $this->orderHistoryRepository->findOrdersForUser($userId);
        $orderDtos = array_map([OrderSummaryDto::class, 'fromRow'], $orderRows);

        $ticketsByOrder = $this->fetchTicketsForPaidOrders($orderDtos);

        $viewModel = OrderHistoryMapper::toMyOrdersViewModel($orderDtos, $ticketsByOrder, true);

        $this->renderView(__DIR__ . '/../Views/pages/my-orders.php', $viewModel);
    }

    /**
     * Fetches ticket PDF paths for all paid orders, grouped by order ID.
     *
     * @param OrderSummaryDto[] $orderDtos
     * @return array<int, TicketPdfDto[]>
     */
    private function fetchTicketsForPaidOrders(array $orderDtos): array
    {
        $ticketsByOrder = [];

        foreach ($orderDtos as $dto) {
            if ($dto->status !== 'Paid') {
                continue;
            }

            $rows = $this->orderHistoryRepository->findTicketPdfPathsForOrder($dto->orderId);
            $ticketsByOrder[$dto->orderId] = array_map([TicketPdfDto::class, 'fromRow'], $rows);
        }

        return $ticketsByOrder;
    }
}
