<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;

/**
 * Represents a single row from the `Order` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Order
{
    /*
     * Purpose: Stores order header data including status, totals,
     * and payment deadline for ticket purchases.
     */

    public function __construct(
        public readonly int                 $orderId,
        public readonly string              $orderNumber,
        public readonly int                 $userAccountId,
        public readonly int                 $programId,
        public readonly OrderStatus         $status,
        public readonly \DateTimeImmutable  $createdAtUtc,
        public readonly ?\DateTimeImmutable $payBeforeUtc,
        public readonly string              $subtotal,
        public readonly string              $vatTotal,
        public readonly string              $totalAmount,
    )
    {
    }

    /**
     * Creates an Order instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            orderId: (int)$row['OrderId'],
            orderNumber: (string)$row['OrderNumber'],
            userAccountId: (int)$row['UserAccountId'],
            programId: (int)$row['ProgramId'],
            status: OrderStatus::from($row['Status']),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            payBeforeUtc: isset($row['PayBeforeUtc']) ? new \DateTimeImmutable($row['PayBeforeUtc']) : null,
            subtotal: (string)$row['Subtotal'],
            vatTotal: (string)$row['VatTotal'],
            totalAmount: (string)$row['TotalAmount'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'OrderId' => $this->orderId,
            'OrderNumber' => $this->orderNumber,
            'UserAccountId' => $this->userAccountId,
            'ProgramId' => $this->programId,
            'Status' => $this->status->value,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'PayBeforeUtc' => $this->payBeforeUtc?->format('Y-m-d H:i:s'),
            'Subtotal' => $this->subtotal,
            'VatTotal' => $this->vatTotal,
            'TotalAmount' => $this->totalAmount,
        ];
    }
}
