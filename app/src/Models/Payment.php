<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

/**
 * Represents a single row from the `Payment` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Payment
{
    /*
     * Purpose: Tracks payment attempts for orders including method,
     * status, and provider reference.
     */

    public function __construct(
        public readonly int                 $paymentId,
        public readonly int                 $orderId,
        public readonly PaymentMethod       $method,
        public readonly PaymentStatus       $status,
        public readonly ?string             $providerRef,
        public readonly \DateTimeImmutable  $createdAtUtc,
        public readonly ?\DateTimeImmutable $paidAtUtc,
    )
    {
    }

    /**
     * Creates a Payment instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            paymentId: (int)$row['PaymentId'],
            orderId: (int)$row['OrderId'],
            method: PaymentMethod::from($row['Method']),
            status: PaymentStatus::from($row['Status']),
            providerRef: $row['ProviderRef'] ?? null,
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            paidAtUtc: isset($row['PaidAtUtc']) ? new \DateTimeImmutable($row['PaidAtUtc']) : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'PaymentId' => $this->paymentId,
            'OrderId' => $this->orderId,
            'Method' => $this->method->value,
            'Status' => $this->status->value,
            'ProviderRef' => $this->providerRef,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'PaidAtUtc' => $this->paidAtUtc?->format('Y-m-d H:i:s'),
        ];
    }
}
