<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `InvoiceLine` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class InvoiceLine
{
    /*
     * Purpose: Stores individual line items on an invoice with
     * quantity, pricing, and VAT breakdown.
     */

    public function __construct(
        public readonly int     $invoiceLineId,
        public readonly int     $invoiceId,
        public readonly string  $lineDescription,
        public readonly int     $quantity,
        public readonly string  $unitPrice,
        public readonly string  $vatRate,
        public readonly string  $lineSubtotal,
        public readonly string  $lineVatAmount,
        public readonly string  $lineTotal,
        public readonly ?string $donationAmount,
    )
    {
    }

    /**
     * Creates an InvoiceLine instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            invoiceLineId: (int)$row['InvoiceLineId'],
            invoiceId: (int)$row['InvoiceId'],
            lineDescription: (string)$row['LineDescription'],
            quantity: (int)$row['Quantity'],
            unitPrice: (string)$row['UnitPrice'],
            vatRate: (string)$row['VatRate'],
            lineSubtotal: (string)$row['LineSubtotal'],
            lineVatAmount: (string)$row['LineVatAmount'],
            lineTotal: (string)$row['LineTotal'],
            donationAmount: $row['DonationAmount'] ?? null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'InvoiceLineId' => $this->invoiceLineId,
            'InvoiceId' => $this->invoiceId,
            'LineDescription' => $this->lineDescription,
            'Quantity' => $this->quantity,
            'UnitPrice' => $this->unitPrice,
            'VatRate' => $this->vatRate,
            'LineSubtotal' => $this->lineSubtotal,
            'LineVatAmount' => $this->lineVatAmount,
            'LineTotal' => $this->lineTotal,
            'DonationAmount' => $this->donationAmount,
        ];
    }
}
