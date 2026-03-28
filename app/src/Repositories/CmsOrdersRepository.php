<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Checkout\OrderWithDetails;
use App\Repositories\Interfaces\ICmsOrdersRepository;

/**
 * Fetches orders joined with user email, item summary, and latest payment status
 * for the CMS orders list page.
 */
class CmsOrdersRepository extends BaseRepository implements ICmsOrdersRepository
{
    /**
     * Returns all orders with joined details, optionally filtered by status.
     *
     * @return OrderWithDetails[]
     */
    public function findOrdersWithDetails(?string $statusFilter = null): array
    {
        $sql    = $this->buildQuery($statusFilter !== null);
        $params = $statusFilter !== null ? [':status' => $statusFilter] : [];

        return $this->fetchAll($sql, $params, fn(array $row) => OrderWithDetails::fromRow($row));
    }

    /**
     * Builds the orders listing query with two correlated subqueries:
     * - ItemsSummary: concatenates each order's line items (event title, tour, or pass)
     * - PaymentStatus: picks the most recent payment status for the order
     */
    private function buildQuery(bool $withStatusFilter): string
    {
        return "
            SELECT
                o.OrderId,
                o.OrderNumber,
                o.UserAccountId,
                o.Status,
                o.TotalAmount,
                o.CreatedAtUtc,
                ua.Email,
                {$this->buildItemsSummarySelect()} AS ItemsSummary,
                {$this->buildPaymentStatusSelect()} AS PaymentStatus
            FROM `Order` o
            LEFT JOIN UserAccount ua ON o.UserAccountId = ua.UserAccountId
            WHERE 1 = 1{$this->buildStatusClause($withStatusFilter)}
            ORDER BY o.CreatedAtUtc DESC
        ";
    }

    private function buildStatusClause(bool $withStatusFilter): string
    {
        return $withStatusFilter ? ' AND o.Status = :status' : '';
    }

    private function buildItemsSummarySelect(): string
    {
        return "(
            SELECT GROUP_CONCAT(
                CONCAT(oi.Quantity, 'x ',
                    COALESCE(
                        (SELECT e.Title FROM Event e
                         JOIN EventSession es ON e.EventId = es.EventId
                         WHERE es.EventSessionId = oi.EventSessionId),
                        (SELECT CONCAT('History Tour #', oi.HistoryTourId)),
                        (SELECT CONCAT('Pass #', oi.PassPurchaseId)),
                        'Unknown item'
                    )
                ) SEPARATOR ', '
            )
            FROM OrderItem oi
            WHERE oi.OrderId = o.OrderId
        )";
    }

    private function buildPaymentStatusSelect(): string
    {
        return "(
            SELECT p.Status
            FROM Payment p
            WHERE p.OrderId = o.OrderId
            ORDER BY p.CreatedAtUtc DESC
            LIMIT 1
        )";
    }
}
