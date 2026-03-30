<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IOrderHistoryRepository;

/**
 * Reads order history data for the customer-facing "My Orders" page.
 * Uses subqueries to embed payment status and item counts directly
 * into each order row, avoiding extra round-trips.
 */
final class OrderHistoryRepository extends BaseRepository implements IOrderHistoryRepository
{
    /** @inheritDoc */
    public function findOrdersForUser(int $userId): array
    {
        $sql = <<<'SQL'
            SELECT o.*,
                (SELECT p.Status
                 FROM Payment p
                 WHERE p.OrderId = o.OrderId
                 ORDER BY p.CreatedAtUtc DESC
                 LIMIT 1) AS PaymentStatus,
                (SELECT COUNT(*)
                 FROM OrderItem oi
                 WHERE oi.OrderId = o.OrderId) AS ItemCount
            FROM `Order` o
            WHERE o.UserAccountId = :userId
            ORDER BY o.CreatedAtUtc DESC
        SQL;

        $statement = $this->execute($sql, [':userId' => $userId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @inheritDoc */
    public function findTicketPdfPathsForOrder(int $orderId): array
    {
        $sql = <<<'SQL'
            SELECT t.TicketCode, ma.FilePath
            FROM Ticket t
            JOIN OrderItem oi ON t.OrderItemId = oi.OrderItemId
            LEFT JOIN MediaAsset ma ON t.PdfAssetId = ma.MediaAssetId
            WHERE oi.OrderId = :orderId AND t.PdfAssetId IS NOT NULL
            ORDER BY t.TicketId ASC
        SQL;

        $statement = $this->execute($sql, [':orderId' => $orderId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
