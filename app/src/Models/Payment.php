<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

/**
 * Represents a row in the Payment table.
 *
 * Tracks payment attempts for orders — method, status, Stripe session/intent IDs,
 * and provider reference.
 */
final readonly class Payment
{
    /*
     * Purpose: Tracks payment attempts for orders including method,
     * status, and provider reference.
     */

    public function __construct(
        public int                 $paymentId,
        public int                 $orderId,
        public PaymentMethod       $method,
        public PaymentStatus       $status,
        public ?string             $providerRef,
        public \DateTimeImmutable  $createdAtUtc,
        public ?\DateTimeImmutable $paidAtUtc,
    ) {
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
