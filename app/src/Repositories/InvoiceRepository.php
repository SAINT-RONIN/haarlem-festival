<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Repositories\Interfaces\IInvoiceRepository;

/**
 * PDO-backed repository for Invoice and InvoiceLine persistence.
 */
class InvoiceRepository extends BaseRepository implements IInvoiceRepository
{
    /** Inserts the invoice header row and returns the new invoice id. */
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
    ): int {
        return $this->executeInsert(
            'INSERT INTO Invoice (OrderId, InvoiceNumber, InvoiceDateUtc, ClientName, PhoneNumber, AddressLine,
                EmailAddress, SubtotalAmount, TotalVatAmount, TotalAmount, PaymentDateUtc)
             VALUES (:orderId, :invoiceNumber, :invoiceDateUtc, :clientName, :phoneNumber, :addressLine,
                :emailAddress, :subtotalAmount, :totalVatAmount, :totalAmount, :paymentDateUtc)',
            [
                ':orderId' => $orderId,
                ':invoiceNumber' => $invoiceNumber,
                ':invoiceDateUtc' => $invoiceDateUtc->format('Y-m-d H:i:s'),
                ':clientName' => $clientName,
                ':phoneNumber' => $phoneNumber,
                ':addressLine' => $addressLine,
                ':emailAddress' => $emailAddress,
                ':subtotalAmount' => $subtotalAmount,
                ':totalVatAmount' => $totalVatAmount,
                ':totalAmount' => $totalAmount,
                ':paymentDateUtc' => $paymentDateUtc?->format('Y-m-d H:i:s'),
            ],
        );
    }

    /** Inserts one invoice line row for a previously created invoice. */
    public function createInvoiceLine(
        int $invoiceId,
        string $lineDescription,
        int $quantity,
        string $unitPrice,
        string $vatRate,
        string $lineSubtotal,
    ): int {
        return $this->executeInsert(
            'INSERT INTO InvoiceLine (InvoiceId, LineDescription, Quantity, UnitPrice, VatRate, LineSubtotal)
             VALUES (:invoiceId, :lineDescription, :quantity, :unitPrice, :vatRate, :lineSubtotal)',
            [
                ':invoiceId' => $invoiceId,
                ':lineDescription' => $lineDescription,
                ':quantity' => $quantity,
                ':unitPrice' => $unitPrice,
                ':vatRate' => $vatRate,
                ':lineSubtotal' => $lineSubtotal,
            ],
        );
    }

    /** Loads the invoice record that belongs to one order, or null when it does not exist yet. */
    public function findByOrderId(int $orderId): ?Invoice
    {
        return $this->fetchOne(
            'SELECT * FROM Invoice WHERE OrderId = :orderId',
            [':orderId' => $orderId],
            fn(array $row) => Invoice::fromRow($row),
        );
    }

    /**
     * Returns every invoice line for one invoice in display order.
     *
     * @return InvoiceLine[]
     */
    public function findLinesByInvoiceId(int $invoiceId): array
    {
        return $this->fetchAll(
            'SELECT * FROM InvoiceLine WHERE InvoiceId = :invoiceId ORDER BY InvoiceLineId ASC',
            [':invoiceId' => $invoiceId],
            fn(array $row) => InvoiceLine::fromRow($row),
        );
    }

    /** Links a stored PDF media asset to the invoice after the PDF is generated. */
    public function updatePdfAssetId(int $invoiceId, int $pdfAssetId): void
    {
        $this->execute(
            'UPDATE Invoice SET PdfAssetId = :pdfAssetId WHERE InvoiceId = :invoiceId',
            [':pdfAssetId' => $pdfAssetId, ':invoiceId' => $invoiceId],
        );
    }
}
