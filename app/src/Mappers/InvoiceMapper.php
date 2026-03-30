<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Invoice\InvoiceDocumentData;
use App\DTOs\Invoice\InvoiceEmailMessage;
use App\DTOs\Invoice\InvoiceLineData;
use App\Models\Invoice;
use App\Models\InvoiceLine;

/**
 * Maps invoice domain models to DTOs for PDF generation and email delivery.
 */
class InvoiceMapper
{
    /**
     * @param InvoiceLine[] $lines
     */
    public static function toDocumentData(Invoice $invoice, array $lines, string $orderNumber): InvoiceDocumentData
    {
        return new InvoiceDocumentData(
            invoiceNumber: $invoice->invoiceNumber,
            invoiceDateFormatted: $invoice->invoiceDateUtc->format('d M Y'),
            clientName: $invoice->clientName,
            clientEmail: $invoice->emailAddress,
            clientAddress: $invoice->addressLine,
            clientPhone: $invoice->phoneNumber,
            lines: array_map(fn(InvoiceLine $line) => self::toLineData($line), $lines),
            subtotal: $invoice->subtotalAmount,
            totalVat: $invoice->totalVatAmount,
            totalAmount: $invoice->totalAmount,
            paymentDateFormatted: $invoice->paymentDateUtc !== null
                ? $invoice->paymentDateUtc->format('d M Y')
                : $invoice->invoiceDateUtc->format('d M Y'),
            orderNumber: $orderNumber,
        );
    }

    public static function toLineData(InvoiceLine $line): InvoiceLineData
    {
        return new InvoiceLineData(
            description: $line->lineDescription,
            quantity: $line->quantity,
            unitPrice: $line->unitPrice,
            vatRate: $line->vatRate,
            lineSubtotal: $line->lineSubtotal,
        );
    }

    public static function toEmailMessage(
        string $recipientEmail,
        string $recipientName,
        string $orderNumber,
        string $invoiceNumber,
        string $pdfAbsolutePath,
        string $pdfFileName,
    ): InvoiceEmailMessage {
        return new InvoiceEmailMessage(
            recipientEmail: $recipientEmail,
            recipientName: $recipientName,
            orderNumber: $orderNumber,
            invoiceNumber: $invoiceNumber,
            attachments: [
                (object)[
                    'absolutePath' => $pdfAbsolutePath,
                    'displayName' => $pdfFileName,
                ],
            ],
        );
    }

    /**
     * Builds a human-readable line description from an order item row with joined event/pass data.
     */
    public static function buildLineDescription(array $orderItemRow): string
    {
        if (!empty($orderItemRow['PassTypeName'])) {
            return (string)$orderItemRow['PassTypeName'];
        }

        $title = (string)($orderItemRow['EventTitle'] ?? 'Event');
        $date = isset($orderItemRow['StartDateTime'])
            ? (new \DateTimeImmutable($orderItemRow['StartDateTime']))->format('d M Y')
            : '';

        return $date !== '' ? $title . ' - ' . $date : $title;
    }
}
