<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the InvoiceLine table.
 *
 * Each line records one item on the invoice with description, quantity,
 * unit price, VAT rate, and computed subtotal.
 */
final readonly class InvoiceLine
{
    public function __construct(
        public int    $invoiceLineId,
        public int    $invoiceId,
        public string $lineDescription,
        public int    $quantity,
        public string $unitPrice,
        public string $vatRate,
        public string $lineSubtotal,
    ) {}

    /**
     * Creates an InvoiceLine instance from a database row array.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            invoiceLineId: (int) $row['InvoiceLineId'],
            invoiceId: (int) $row['InvoiceId'],
            lineDescription: (string) $row['LineDescription'],
            quantity: (int) $row['Quantity'],
            unitPrice: (string) $row['UnitPrice'],
            vatRate: (string) $row['VatRate'],
            lineSubtotal: (string) $row['LineSubtotal'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
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
        ];
    }
}
