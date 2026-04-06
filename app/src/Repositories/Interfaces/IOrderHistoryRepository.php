<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Domain\OrderHistory\OrderSummaryDto;
use App\DTOs\Domain\OrderHistory\TicketPdfDto;

/**
 * Contract for fetching order history data for the customer-facing "My Orders" page.
 * Provides pre-aggregated summaries (item counts, latest payment status) to avoid
 * N+1 queries in the controller layer.
 */
interface IOrderHistoryRepository
{
    /**
     * Returns order summaries for a given user, newest first.
     * Each row includes the latest payment status and total item count.
     *
     * @return OrderSummaryDto[]
     */
    public function findOrdersForUser(int $userId): array;

    /**
     * Returns ticket PDF paths for all tickets belonging to a given order
     * that have a generated PDF asset.
     *
     * @return TicketPdfDto[]
     */
    public function findTicketPdfPathsForOrder(int $orderId): array;
}
