<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Interfaces\IOrderItemRepository;
use PDO;

/**
 * Manages the OrderItem table, which stores individual line items within an order.
 * Each item links to exactly one purchasable entity (event session, history tour, or pass)
 * via mutually exclusive nullable foreign keys.
 */
class OrderItemRepository implements IOrderItemRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

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
        $stmt = $this->pdo->prepare('
            INSERT INTO OrderItem (
                OrderId,
                EventSessionId,
                HistoryTourId,
                PassPurchaseId,
                Quantity,
                UnitPrice,
                VatRate,
                DonationAmount,
                SpecialRequest
            ) VALUES (
                :orderId,
                :eventSessionId,
                :historyTourId,
                :passPurchaseId,
                :quantity,
                :unitPrice,
                :vatRate,
                :donationAmount,
                :specialRequest
            )
        ');

        $stmt->execute([
            'orderId' => $orderId,
            'eventSessionId' => $eventSessionId,
            'historyTourId' => $historyTourId,
            'passPurchaseId' => $passPurchaseId,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'vatRate' => $vatRate,
            'donationAmount' => $donationAmount,
            'specialRequest' => $specialRequest,
        ]);
    }

    /**
     * Checks whether any order item references the given session. Used to prevent
     * deletion of sessions that already have orders placed against them.
     */
    public function existsForSession(int $sessionId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM OrderItem WHERE EventSessionId = :sessionId LIMIT 1'
        );
        $stmt->execute([':sessionId' => $sessionId]);
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
        $stmt = $this->pdo->prepare('SELECT * FROM OrderItem WHERE OrderId = :orderId');
        $stmt->execute([':orderId' => $orderId]);

        return array_map([OrderItem::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

