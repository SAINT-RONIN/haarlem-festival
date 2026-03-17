<?php

declare(strict_types=1);

namespace App\Services;

use App\Infrastructure\Database;
use PDO;

/**
 * Service for the CMS Orders list page.
 *
 * Fetches orders joined with user email, items summary, and latest payment status.
 * Returns raw rows — the controller maps them to ViewModels.
 */
class CmsOrdersService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all orders with user email, item summary, and latest payment status.
     * Optionally filtered by order status.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrdersWithDetails(?string $statusFilter = null): array
    {
        $sql    = $this->buildOrdersQuery($statusFilter !== null);
        $params = $this->buildQueryParams($statusFilter);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Builds the SQL query for the orders list.
     */
    private function buildOrdersQuery(bool $withStatusFilter): string
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

    /**
     * Builds the named parameter array for the query.
     *
     * @return array<string, mixed>
     */
    private function buildQueryParams(?string $statusFilter): array
    {
        if ($statusFilter === null) {
            return [];
        }

        return [':status' => $statusFilter];
    }
}
