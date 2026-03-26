<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Interfaces\IOrderItemRepository;

/**
 * Manages the OrderItem table, which stores individual line items within an order.
 * Each item links to exactly one purchasable entity (event session, history tour, or pass)
 * via mutually exclusive nullable foreign keys.
 */
class OrderItemRepository extends BaseRepository implements IOrderItemRepository
{
    /**
     * Inserts a line item into an order. Exactly one of eventSessionId, historyTourId,
     * or passPurchaseId should be non-null to identify the purchased product type.
     * Monetary values are strings to preserve decimal precision.
     */
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

    /**
     * Checks whether any order item references the given session. Used to prevent
     * deletion of sessions that already have orders placed against them.
     */
    public function existsForSession(int $sessionId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM OrderItem WHERE EventSessionId = :sessionId LIMIT 1',
            [':sessionId' => $sessionId],
        );

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Returns all order items belonging to the given order.
     * Used to restore session capacity when an order is cancelled or expires.
     *
     * @return OrderItem[]
     */
    public function findByOrderId(int $orderId): array
    {
        // Fetch all line items for a specific order
        return $this->fetchAll(
            'SELECT * FROM OrderItem WHERE OrderId = :orderId',
            [':orderId' => $orderId],
            fn(array $row) => OrderItem::fromRow($row),
        );
    }
}

