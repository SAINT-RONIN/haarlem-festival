<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

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
     * @return array<int, array<string, mixed>>
     */
    public function findOrdersForUser(int $userId): array;

    /**
     * Returns ticket PDF paths for all tickets belonging to a given order
     * that have a generated PDF asset.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTicketPdfPathsForOrder(int $orderId): array;
}
