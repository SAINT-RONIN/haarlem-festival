<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `OrderItem` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class OrderItem
{
    /*
     * Purpose: Stores individual items in an order (tickets, passes, tours)
     * with pricing and optional special requests.
     */

    public function __construct(
        public int $orderItemId,
        public int $orderId,
        public ?int $eventSessionId,
        public ?int $historyTourId,
        public ?int $passPurchaseId,
        public int $quantity,
        public string $unitPrice,
        public string $vatRate,
        public ?string $donationAmount,
        public string $specialRequest,
    ) {
    }

    /**
     * Creates an OrderItem instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            orderItemId: (int) $row['OrderItemId'],
            orderId: (int) $row['OrderId'],
            eventSessionId: isset($row['EventSessionId']) ? (int) $row['EventSessionId'] : null,
            historyTourId: isset($row['HistoryTourId']) ? (int) $row['HistoryTourId'] : null,
            passPurchaseId: isset($row['PassPurchaseId']) ? (int) $row['PassPurchaseId'] : null,
            quantity: (int) $row['Quantity'],
            unitPrice: (string) $row['UnitPrice'],
            vatRate: (string) $row['VatRate'],
            donationAmount: $row['DonationAmount'] ?? null,
            specialRequest: (string) $row['SpecialRequest'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'OrderItemId' => $this->orderItemId,
            'OrderId' => $this->orderId,
            'EventSessionId' => $this->eventSessionId,
            'HistoryTourId' => $this->historyTourId,
            'PassPurchaseId' => $this->passPurchaseId,
            'Quantity' => $this->quantity,
            'UnitPrice' => $this->unitPrice,
            'VatRate' => $this->vatRate,
            'DonationAmount' => $this->donationAmount,
            'SpecialRequest' => $this->specialRequest,
        ];
    }
}
