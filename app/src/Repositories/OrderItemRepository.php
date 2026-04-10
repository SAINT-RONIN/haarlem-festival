<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Interfaces\IOrderItemRepository;

// Each item links to exactly one purchasable entity via mutually exclusive nullable FKs:
// EventSessionId, HistoryTourId, or PassPurchaseId. Monetary values are strings for decimal precision.
class OrderItemRepository extends BaseRepository implements IOrderItemRepository
{
    public function create(
        int $orderId,
        ?int $eventSessionId,
        ?int $historyTourId,
        ?int $passPurchaseId,
        int $quantity,
        string $unitPrice,
        string $vatRate,
        ?string $donationAmount,
        string $specialRequest = '',
    ): void {
        $this->execute(
            'INSERT INTO OrderItem (
                OrderId, EventSessionId, HistoryTourId, PassPurchaseId,
                Quantity, UnitPrice, VatRate, DonationAmount, SpecialRequest
            ) VALUES (
                :orderId, :eventSessionId, :historyTourId, :passPurchaseId,
                :quantity, :unitPrice, :vatRate, :donationAmount, :specialRequest
            )',
            [
                'orderId' => $orderId,
                'eventSessionId' => $eventSessionId,
                'historyTourId' => $historyTourId,
                'passPurchaseId' => $passPurchaseId,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'vatRate' => $vatRate,
                'donationAmount' => $donationAmount,
                'specialRequest' => $specialRequest,
            ],
        );
    }

    // Prevents deletion of sessions that already have orders.
    public function existsForSession(int $sessionId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM OrderItem WHERE EventSessionId = :sessionId LIMIT 1',
            [':sessionId' => $sessionId],
        );

        return $stmt->fetchColumn() !== false;
    }

    public function findByOrderId(int $orderId): array
    {
        return $this->fetchAll(
            'SELECT * FROM OrderItem WHERE OrderId = :orderId',
            [':orderId' => $orderId],
            fn(array $row) => OrderItem::fromRow($row),
        );
    }

    public function findById(int $orderItemId): ?OrderItem
    {
        return $this->fetchOne(
            'SELECT * FROM OrderItem WHERE OrderItemId = :orderItemId',
            [':orderItemId' => $orderItemId],
            fn(array $row) => OrderItem::fromRow($row),
        );
    }
}
