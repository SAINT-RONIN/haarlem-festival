<?php

declare(strict_types=1);

namespace App\DTOs\Invoice;

/**
 * Single line item for the invoice PDF document.
 */
final readonly class InvoiceLineData
{
    public function __construct(
        public string $description,
        public int    $quantity,
        public string $unitPrice,
        public string $vatRate,
        public string $lineSubtotal,
    ) {
    }
}
