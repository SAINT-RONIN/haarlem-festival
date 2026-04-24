<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\OrderHistory\OrderSummaryData;
use App\DTOs\Domain\OrderHistory\TicketPdfData;
use App\Repositories\Interfaces\IOrderHistoryRepository;
use App\Services\Interfaces\IOrderHistoryService;

class OrderHistoryService implements IOrderHistoryService
{
    public function __construct(
        private readonly IOrderHistoryRepository $orderHistoryRepository,
    ) {}

    /**
     * @return array{orders: OrderSummaryData[], ticketsByOrder: array<int, TicketPdfData[]>}
     */
    public function getOrderHistoryData(int $userId): array
    {
        $orderDtos = $this->orderHistoryRepository->findOrdersForUser($userId);

        return [
            'orders' => $orderDtos,
            'ticketsByOrder' => $this->fetchTicketsForPaidOrders($orderDtos),
        ];
    }

    /**
     * @param OrderSummaryData[] $orderDtos
     * @return array<int, TicketPdfData[]>
     */
    private function fetchTicketsForPaidOrders(array $orderDtos): array
    {
        $ticketsByOrder = [];

        foreach ($orderDtos as $dto) {
            if ($dto->status !== 'Paid') {
                continue;
            }

            $ticketsByOrder[$dto->orderId] = $this->orderHistoryRepository->findTicketPdfPathsForOrder($dto->orderId);
        }

        return $ticketsByOrder;
    }
}
