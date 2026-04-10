<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Invoice;

/**
 * Complete data needed to render an invoice PDF document.
 *
 * @param InvoiceLineData[] $lines
 */
final readonly class InvoiceDocumentData
{
    /**
     * @param InvoiceLineData[] $lines
     */
    public function __construct(
        public string $invoiceNumber,
        public string $invoiceDateFormatted,
        public string $clientName,
        public string $clientEmail,
        public string $clientAddress,
        public string $clientPhone,
        public array  $lines,
        public string $subtotal,
        public string $totalVat,
        public string $totalAmount,
        public string $paymentDateFormatted,
        public string $orderNumber,
    ) {}
}
