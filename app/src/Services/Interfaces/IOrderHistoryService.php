<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\OrderHistory\OrderSummaryDto;
use App\DTOs\Domain\OrderHistory\TicketPdfDto;

/**
 * Defines the contract for loading customer order history data.
 */
interface IOrderHistoryService
{
    /**
     * Returns order history DTOs plus ticket PDF DTOs grouped by order ID.
     *
     * @return array{orders: OrderSummaryDto[], ticketsByOrder: array<int, TicketPdfDto[]>}
     */
    public function getOrderHistoryData(int $userId): array;
}
