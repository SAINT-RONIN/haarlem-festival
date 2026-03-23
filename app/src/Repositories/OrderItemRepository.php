<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IOrderItemRepository;
use PDO;

/**
 * Manages the OrderItem table, which stores individual line items within an order.
 * Each item links to exactly one purchasable entity (event session, history tour, or pass)
 * via mutually exclusive nullable foreign keys.
 */
class OrderItemRepository implements IOrderItemRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
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
}

