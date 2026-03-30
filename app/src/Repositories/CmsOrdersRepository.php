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

    /** @inheritDoc */
    public function findOrderById(int $orderId): ?array
    {
        $sql = "
            SELECT o.*, ua.Email AS UserEmail
            FROM `Order` o
            LEFT JOIN UserAccount ua ON o.UserAccountId = ua.UserAccountId
            WHERE o.OrderId = :orderId
        ";

        $statement = $this->execute($sql, [':orderId' => $orderId]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /** @inheritDoc */
    public function findOrderItems(int $orderId): array
    {
        $sql = "
            SELECT oi.*, e.Title AS EventTitle, v.Name AS VenueName, es.SessionDateTime, pt.PassName
            FROM OrderItem oi
            LEFT JOIN EventSession es ON oi.EventSessionId = es.EventSessionId
            LEFT JOIN Event e ON es.EventId = e.EventId
            LEFT JOIN Venue v ON es.VenueId = v.VenueId
            LEFT JOIN PassPurchase pp ON oi.PassPurchaseId = pp.PassPurchaseId
            LEFT JOIN PassType pt ON pp.PassTypeId = pt.PassTypeId
            WHERE oi.OrderId = :orderId
            ORDER BY oi.OrderItemId ASC
        ";

        $statement = $this->execute($sql, [':orderId' => $orderId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @inheritDoc */
    public function findOrderPayments(int $orderId): array
    {
        $sql = "
            SELECT * FROM Payment
            WHERE OrderId = :orderId
            ORDER BY CreatedAtUtc DESC
        ";

        $statement = $this->execute($sql, [':orderId' => $orderId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @inheritDoc */
    public function findOrderTickets(int $orderId): array
    {
        $sql = "
            SELECT t.*, ua.Username AS ScannedByUserName, ma.FilePath AS PdfAssetPath
            FROM Ticket t
            JOIN OrderItem oi ON t.OrderItemId = oi.OrderItemId
            LEFT JOIN UserAccount ua ON t.ScannedByUserId = ua.UserAccountId
            LEFT JOIN MediaAsset ma ON t.PdfAssetId = ma.MediaAssetId
            WHERE oi.OrderId = :orderId
            ORDER BY t.TicketId ASC
        ";

        $statement = $this->execute($sql, [':orderId' => $orderId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
