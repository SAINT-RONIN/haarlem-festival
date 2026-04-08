<?php

declare(strict_types=1);

namespace App\DTOs\Domain\OrderHistory;

/**
 * Flat summary of a single order for the "My Orders" list.
 * Includes the latest payment status and item count from subqueries
 * so the controller doesn't need additional repository calls.
 */
final readonly class OrderSummaryData
{
    public function __construct(
        public int $orderId,
        public string $orderNumber,
        public string $status,
        public string $totalAmount,
        public string $createdAtUtc,
        public ?string $paymentStatus,
        public int $itemCount,
        public ?string $payBeforeUtc,
    ) {
    }

    /** Creates an instance from a raw database row. */
    public static function fromRow(array $row): self
    {
        return new self(
            orderId: (int) $row['OrderId'],
            orderNumber: (string) $row['OrderNumber'],
            status: (string) $row['Status'],
            totalAmount: (string) $row['TotalAmount'],
            createdAtUtc: (string) $row['CreatedAtUtc'],
            paymentStatus: isset($row['PaymentStatus']) ? (string) $row['PaymentStatus'] : null,
            itemCount: (int) $row['ItemCount'],
            payBeforeUtc: isset($row['PayBeforeUtc']) ? (string) $row['PayBeforeUtc'] : null,
        );
    }
}
