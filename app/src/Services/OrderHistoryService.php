<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OrderHistory\OrderSummaryDto;
use App\DTOs\OrderHistory\TicketPdfDto;
use App\Repositories\Interfaces\IOrderHistoryRepository;
use App\Services\Interfaces\IOrderHistoryService;

/**
 * Loads customer order history and groups ticket PDF data for display.
 */
class OrderHistoryService implements IOrderHistoryService
{
    public function __construct(
        private readonly IOrderHistoryRepository $orderHistoryRepository,
    ) {
    }

    /**
     * @return array{orders: OrderSummaryDto[], ticketsByOrder: array<int, TicketPdfDto[]>}
     */
    public function getOrderHistoryData(int $userId): array
    {
        $orderRows = $this->orderHistoryRepository->findOrdersForUser($userId);
        $orderDtos = array_map([OrderSummaryDto::class, 'fromRow'], $orderRows);

        return [
            'orders' => $orderDtos,
            'ticketsByOrder' => $this->fetchTicketsForPaidOrders($orderDtos),
        ];
    }

    /**
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
