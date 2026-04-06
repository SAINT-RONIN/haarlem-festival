<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the Invoice table.
 *
 * Created after successful payment to record invoice details including
 * client information, totals, and a link to the generated PDF asset.
 */
final readonly class Invoice
{
    public function __construct(
        public int                 $invoiceId,
        public int                 $orderId,
        public string              $invoiceNumber,
        public \DateTimeImmutable  $invoiceDateUtc,
        public string              $clientName,
        public string              $phoneNumber,
        public string              $addressLine,
        public string              $emailAddress,
        public string              $subtotalAmount,
        public string              $totalVatAmount,
        public string              $totalAmount,
        public ?\DateTimeImmutable $paymentDateUtc,
        public ?int                $pdfAssetId,
    ) {
    }

    /**
     * Creates an Invoice instance from a database row array.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            invoiceId: (int)$row['InvoiceId'],
            orderId: (int)$row['OrderId'],
            invoiceNumber: (string)$row['InvoiceNumber'],
            invoiceDateUtc: new \DateTimeImmutable($row['InvoiceDateUtc']),
            clientName: (string)$row['ClientName'],
            phoneNumber: (string)($row['PhoneNumber'] ?? ''),
            addressLine: (string)($row['AddressLine'] ?? ''),
            emailAddress: (string)$row['EmailAddress'],
            subtotalAmount: (string)$row['SubtotalAmount'],
            totalVatAmount: (string)$row['TotalVatAmount'],
            totalAmount: (string)$row['TotalAmount'],
            paymentDateUtc: isset($row['PaymentDateUtc']) && $row['PaymentDateUtc'] !== null
                ? new \DateTimeImmutable($row['PaymentDateUtc'])
                : null,
            pdfAssetId: isset($row['PdfAssetId']) ? (int)$row['PdfAssetId'] : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     */
    public function toArray(): array
    {
        return [
            'InvoiceId' => $this->invoiceId,
            'OrderId' => $this->orderId,
            'InvoiceNumber' => $this->invoiceNumber,
            'InvoiceDateUtc' => $this->invoiceDateUtc->format('Y-m-d H:i:s'),
            'ClientName' => $this->clientName,
            'PhoneNumber' => $this->phoneNumber,
            'AddressLine' => $this->addressLine,
            'EmailAddress' => $this->emailAddress,
            'SubtotalAmount' => $this->subtotalAmount,
            'TotalVatAmount' => $this->totalVatAmount,
            'TotalAmount' => $this->totalAmount,
            'PaymentDateUtc' => $this->paymentDateUtc?->format('Y-m-d H:i:s'),
            'PdfAssetId' => $this->pdfAssetId,
        ];
    }
}
