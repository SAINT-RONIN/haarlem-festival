<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use App\Models\InvoiceLine;

/**
 * Contract for managing Invoice and InvoiceLine rows.
 */
interface IInvoiceRepository
{
    /**
     * Creates a new invoice record and returns the auto-generated ID.
     */
    public function createInvoice(
        int $orderId,
        string $invoiceNumber,
        \DateTimeImmutable $invoiceDateUtc,
        string $clientName,
        string $phoneNumber,
        string $addressLine,
        string $emailAddress,
        string $subtotalAmount,
        string $totalVatAmount,
        string $totalAmount,
        ?\DateTimeImmutable $paymentDateUtc,
    ): int;

    /**
     * Creates a new invoice line and returns the auto-generated ID.
     */
    public function createInvoiceLine(
        int $invoiceId,
        string $lineDescription,
        int $quantity,
        string $unitPrice,
        string $vatRate,
        string $lineSubtotal,
    ): int;

    /**
     * Finds an invoice by its associated order ID.
     */
    public function findByOrderId(int $orderId): ?Invoice;

    /**
     * Returns all invoice lines for the given invoice.
     *
     * @return InvoiceLine[]
     */
    public function findLinesByInvoiceId(int $invoiceId): array;

    /**
     * Links a generated PDF asset to the invoice record.
     */
    public function updatePdfAssetId(int $invoiceId, int $pdfAssetId): void;
}
