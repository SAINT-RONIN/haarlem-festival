<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\OrderHistory\OrderSummaryData;
use App\DTOs\Domain\OrderHistory\TicketPdfData;

/**
 * Defines the contract for loading customer order history data.
 */
interface IOrderHistoryService
{
    /**
     * Returns order history DTOs plus ticket PDF DTOs grouped by order ID.
     *
     * @return array{orders: OrderSummaryData[], ticketsByOrder: array<int, TicketPdfData[]>}
     */
    public function getOrderHistoryData(int $userId): array;
}
