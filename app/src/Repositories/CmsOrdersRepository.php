<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderWithDetails;
use PDO;

/**
 * Fetches orders joined with user email, item summary, and latest payment status
 * for the CMS orders list page.
 */
class CmsOrdersRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Returns all orders with joined details, optionally filtered by status.
     *
     * @return OrderWithDetails[]
     */
    public function findOrdersWithDetails(?string $statusFilter = null): array
    {
        $sql    = $this->buildQuery($statusFilter !== null);
        $params = $statusFilter !== null ? [':status' => $statusFilter] : [];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn(array $row) => OrderWithDetails::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    private function buildQuery(bool $withStatusFilter): string
    {
        $statusClause = $withStatusFilter ? ' AND o.Status = :status' : '';

        return "
            SELECT
                o.OrderId,
                o.OrderNumber,
                o.UserAccountId,
                o.Status,
                o.TotalAmount,
                o.CreatedAtUtc,
                ua.Email,
                (
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
                ) AS ItemsSummary,
                (
                    SELECT p.Status
                    FROM Payment p
                    WHERE p.OrderId = o.OrderId
                    ORDER BY p.CreatedAtUtc DESC
                    LIMIT 1
                ) AS PaymentStatus
            FROM `Order` o
            LEFT JOIN UserAccount ua ON o.UserAccountId = ua.UserAccountId
            WHERE 1 = 1{$statusClause}
            ORDER BY o.CreatedAtUtc DESC
        ";
    }
}
