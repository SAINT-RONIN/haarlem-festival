<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\DTOs\Domain\Invoice\InvoiceDocumentData;
use App\DTOs\Domain\Invoice\InvoiceLineData;
use App\Infrastructure\Interfaces\IInvoicePdfGenerator;

/**
 * Generates a professional A4 invoice PDF using raw PDF 1.4 syntax.
 *
 * Layout: navy header, invoice/client info blocks, line items table with
 * alternating row backgrounds, totals section, and a footer message.
 */
final class InvoicePdfGenerator extends BasePdfWriter implements IInvoicePdfGenerator
{
    private const NAVY = '0.10 0.16 0.26';
    private const DARK_TEXT = '0.14 0.18 0.25';
    private const MUTED_TEXT = '0.31 0.37 0.48';
    private const LIGHT_TEXT = '0.49 0.55 0.64';
    private const WHITE = '1 1 1';
    private const ROW_ALT_BG = '0.96 0.97 0.98';
    private const DIVIDER = '0.83 0.86 0.91';

    public function generatePdf(InvoiceDocumentData $data): string
    {
        $content = $this->buildContentStream($data);

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
            "<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream",
        ];

        return $this->compilePdf($objects);
    }

    private function buildContentStream(InvoiceDocumentData $data): string
    {
        $commands = [];
        $commands[] = $this->drawHeader();
        $commands[] = $this->drawInvoiceInfo($data);
        $commands[] = $this->drawClientInfo($data);
        $commands[] = $this->drawLineItemsTable($data);
        $commands[] = $this->drawTotalsSection($data);
        $commands[] = $this->drawFooter();

        return implode("\n", array_filter($commands));
    }

    private function drawHeader(): string
    {
        $commands = [];
        $commands[] = $this->fillRect(0, 792, 595, 50, self::NAVY);
        $commands[] = $this->writeText('INVOICE', 50, 808, 24, true, self::WHITE);
        $commands[] = $this->writeText('Haarlem Festival', 380, 810, 14, true, self::WHITE);

        return implode("\n", $commands);
    }

    private function drawInvoiceInfo(InvoiceDocumentData $data): string
    {
        $commands = [];
        $y = 760;
        $commands[] = $this->writeText('Invoice Number:', 50, $y, 10, true, self::LIGHT_TEXT);
        $commands[] = $this->writeText($data->invoiceNumber, 160, $y, 10, false, self::DARK_TEXT);
        $commands[] = $this->writeText('Invoice Date:', 50, $y - 16, 10, true, self::LIGHT_TEXT);
        $commands[] = $this->writeText($data->invoiceDateFormatted, 160, $y - 16, 10, false, self::DARK_TEXT);
        $commands[] = $this->writeText('Payment Date:', 50, $y - 32, 10, true, self::LIGHT_TEXT);
        $commands[] = $this->writeText($data->paymentDateFormatted, 160, $y - 32, 10, false, self::DARK_TEXT);
        $commands[] = $this->writeText('Order Reference:', 50, $y - 48, 10, true, self::LIGHT_TEXT);
        $commands[] = $this->writeText($data->orderNumber, 160, $y - 48, 10, false, self::DARK_TEXT);

        return implode("\n", $commands);
    }

    private function drawClientInfo(InvoiceDocumentData $data): string
    {
        $commands = [];
        $x = 350;
        $y = 760;
        $commands[] = $this->writeText('Bill To:', $x, $y, 10, true, self::LIGHT_TEXT);
        $commands[] = $this->writeText($data->clientName, $x, $y - 16, 11, true, self::DARK_TEXT);
        $commands[] = $this->writeText($data->clientEmail, $x, $y - 32, 10, false, self::MUTED_TEXT);

        $offset = 48;
        if ($data->clientAddress !== '') {
            $commands[] = $this->writeText($data->clientAddress, $x, $y - $offset, 10, false, self::MUTED_TEXT);
            $offset += 16;
        }
        if ($data->clientPhone !== '') {
            $commands[] = $this->writeText($data->clientPhone, $x, $y - $offset, 10, false, self::MUTED_TEXT);
        }

        return implode("\n", $commands);
    }

    private function drawLineItemsTable(InvoiceDocumentData $data): string
    {
        $commands = [];
        $tableTop = 680;

        // Table header background
        $commands[] = $this->fillRect(40, $tableTop - 4, 515, 20, self::NAVY);
        $commands[] = $this->writeText('Description', 50, $tableTop, 9, true, self::WHITE);
        $commands[] = $this->writeText('Qty', 310, $tableTop, 9, true, self::WHITE);
        $commands[] = $this->writeText('Unit Price', 360, $tableTop, 9, true, self::WHITE);
        $commands[] = $this->writeText('VAT %', 440, $tableTop, 9, true, self::WHITE);
        $commands[] = $this->writeText('Subtotal', 500, $tableTop, 9, true, self::WHITE);

        // Table rows
        $rowY = $tableTop - 24;
        foreach ($data->lines as $index => $line) {
            $commands[] = $this->drawLineItemRow($line, $rowY, $index % 2 === 1);
            $rowY -= 20;
        }

        // Divider below table
        $commands[] = $this->fillRect(40, $rowY + 4, 515, 1, self::DIVIDER);

        return implode("\n", $commands);
    }

    private function drawLineItemRow(InvoiceLineData $line, float $y, bool $alternate): string
    {
        $commands = [];

        if ($alternate) {
            $commands[] = $this->fillRect(40, $y - 4, 515, 20, self::ROW_ALT_BG);
        }

        $description = mb_strlen($line->description) > 42
            ? mb_substr($line->description, 0, 39) . '...'
            : $line->description;

        $commands[] = $this->writeText($description, 50, $y, 9, false, self::DARK_TEXT);
        $commands[] = $this->writeText((string)$line->quantity, 318, $y, 9, false, self::DARK_TEXT);
        $commands[] = $this->writeText($this->formatCurrency($line->unitPrice), 360, $y, 9, false, self::DARK_TEXT);
        $commands[] = $this->writeText($this->formatPercentage($line->vatRate), 440, $y, 9, false, self::DARK_TEXT);
        $commands[] = $this->writeText($this->formatCurrency($line->lineSubtotal), 500, $y, 9, false, self::DARK_TEXT);

        return implode("\n", $commands);
    }

    private function drawTotalsSection(InvoiceDocumentData $data): string
    {
        $commands = [];
        $lineCount = count($data->lines);
        $baseY = 680 - 24 - ($lineCount * 20) - 20;

        $commands[] = $this->writeText('Subtotal:', 400, $baseY, 10, false, self::MUTED_TEXT);
        $commands[] = $this->writeText($this->formatCurrency($data->subtotal), 500, $baseY, 10, false, self::DARK_TEXT);

        $commands[] = $this->writeText('VAT:', 400, $baseY - 18, 10, false, self::MUTED_TEXT);
        $commands[] = $this->writeText($this->formatCurrency($data->totalVat), 500, $baseY - 18, 10, false, self::DARK_TEXT);

        $commands[] = $this->fillRect(390, $baseY - 34, 165, 1, self::NAVY);
        $commands[] = $this->writeText('Total:', 400, $baseY - 48, 12, true, self::NAVY);
        $commands[] = $this->writeText($this->formatCurrency($data->totalAmount), 500, $baseY - 48, 12, true, self::NAVY);

        return implode("\n", $commands);
    }

    private function drawFooter(): string
    {
        $commands = [];
        $commands[] = $this->fillRect(40, 60, 515, 1, self::DIVIDER);
        $commands[] = $this->writeText('Thank you for attending Haarlem Festival', 185, 40, 10, false, self::MUTED_TEXT);

        return implode("\n", $commands);
    }

    private function formatCurrency(string $amount): string
    {
        return chr(0xA4) . ' ' . number_format((float)$amount, 2, '.', ',');
    }

    private function formatPercentage(string $rate): string
    {
        $percent = (float)$rate * 100;
        return number_format($percent, 0) . '%';
    }
}
