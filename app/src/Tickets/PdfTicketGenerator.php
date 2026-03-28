<?php

declare(strict_types=1);

namespace App\Tickets;

use App\DTOs\Tickets\QrCodeMatrix;
use App\DTOs\Tickets\TicketDocumentData;
use App\Tickets\Interfaces\ITicketPdfGenerator;

/**
 * Generates a lightweight one-page PDF ticket using raw PDF syntax.
 */
final class PdfTicketGenerator implements ITicketPdfGenerator
{
    public function generatePdf(TicketDocumentData $document, QrCodeMatrix $qrCode): string
    {
        $content = $this->buildContentStream($document, $qrCode);

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

    private function buildContentStream(TicketDocumentData $document, QrCodeMatrix $qrCode): string
    {
        $commands = [];
        $commands[] = $this->fillRect(36, 772, 523, 40, '0.10 0.16 0.26');
        $commands[] = $this->writeText('Haarlem Festival Ticket', 56, 786, 22, true, '1 1 1');
        $commands[] = $this->writeText($document->ticketLabel, 56, 746, 11, false, '0.31 0.37 0.48');
        $commands[] = $this->writeText($document->eventTitle, 56, 718, 20, true, '0.10 0.16 0.26');
        $commands[] = $this->writeWrappedText($document->venueName . ' • ' . $document->eventTypeName, 56, 692, 12, 58, false, '0.31 0.37 0.48');
        $commands[] = $this->drawQrCard($document, $qrCode);
        $commands[] = $this->writeSectionLabel('Order', 336, 640);
        $commands[] = $this->writeText($document->orderReference, 336, 620, 18, true, '0.10 0.16 0.26');
        $commands[] = $this->writeSectionLabel('Ticket code', 336, 576);
        $commands[] = $this->writeText($document->ticketCode, 336, 556, 16, true, '0.10 0.16 0.26');
        $commands[] = $this->writeSectionLabel('Issued to', 336, 512);
        $commands[] = $this->writeText($document->recipientName, 336, 492, 14, false, '0.14 0.18 0.25');
        $commands[] = $this->writeSectionLabel('Date', 336, 448);
        $commands[] = $this->writeText($document->sessionDateLabel, 336, 428, 14, false, '0.14 0.18 0.25');
        $commands[] = $this->writeSectionLabel('Time', 336, 384);
        $commands[] = $this->writeText($document->sessionTimeLabel, 336, 364, 14, false, '0.14 0.18 0.25');
        $commands[] = $this->writeWrappedText(
            'Present this PDF at the venue entrance. The QR code can only be scanned once.',
            56,
            228,
            12,
            86,
            false,
            '0.31 0.37 0.48',
        );
        $commands[] = $this->fillRect(36, 120, 523, 1, '0.83 0.86 0.91');
        $commands[] = $this->writeText('If this QR code has already been scanned, entry will be refused.', 56, 96, 10, false, '0.49 0.55 0.64');

        return implode("\n", array_filter($commands));
    }

    private function drawQrCard(TicketDocumentData $document, QrCodeMatrix $qrCode): string
    {
        $commands = [];
        $commands[] = $this->fillRect(56, 286, 236, 344, '0.98 0.98 0.99');
        $commands[] = $this->strokeRect(56, 286, 236, 344, '0.88 0.90 0.94');
        $commands[] = $this->drawQrMatrix($qrCode, 82, 406, 184);
        $commands[] = $this->writeText('Scan at the venue entrance', 102, 372, 11, true, '0.10 0.16 0.26');
        $commands[] = $this->writeWrappedText(
            $document->eventTitle,
            86,
            344,
            10,
            28,
            false,
            '0.31 0.37 0.48',
        );

        return implode("\n", $commands);
    }

    private function drawQrMatrix(QrCodeMatrix $qrCode, float $originX, float $originY, float $size): string
    {
        $moduleSize = $size / $qrCode->size;
        $commands = [
            $this->fillRect($originX - 10, $originY - 10, $size + 20, $size + 20, '1 1 1'),
            $this->fillRect($originX - 2, $originY - 2, $size + 4, $size + 4, '0.90 0.92 0.95'),
        ];

        for ($row = 0; $row < $qrCode->size; $row++) {
            for ($column = 0; $column < $qrCode->size; $column++) {
                if (!$qrCode->modules[$row][$column]) {
                    continue;
                }

                $x = $originX + ($column * $moduleSize);
                $y = $originY + (($qrCode->size - 1 - $row) * $moduleSize);
                $commands[] = $this->fillRect($x, $y, $moduleSize, $moduleSize, '0 0 0');
            }
        }

        return implode("\n", $commands);
    }

    private function writeSectionLabel(string $text, float $x, float $y): string
    {
        return $this->writeText(strtoupper($text), $x, $y, 10, true, '0.49 0.55 0.64');
    }

    private function writeWrappedText(
        string $text,
        float $x,
        float $y,
        float $fontSize,
        int $maxCharsPerLine,
        bool $bold,
        string $color,
    ): string {
        $lines = $this->wrapText($text, $maxCharsPerLine);
        $commands = [];

        foreach ($lines as $index => $line) {
            $commands[] = $this->writeText($line, $x, $y - ($index * ($fontSize + 4)), $fontSize, $bold, $color);
        }

        return implode("\n", $commands);
    }

    /**
     * @return string[]
     */
    private function wrapText(string $text, int $maxCharsPerLine): array
    {
        $wrapped = wordwrap($text, $maxCharsPerLine, "\n", true);
        return explode("\n", $wrapped);
    }

    private function writeText(
        string $text,
        float $x,
        float $y,
        float $fontSize,
        bool $bold,
        string $color,
    ): string {
        $font = $bold ? 'F2' : 'F1';
        $escaped = $this->escapePdfText($text);

        return sprintf(
            "BT\n%s rg\n/%s %.2F Tf\n1 0 0 1 %.2F %.2F Tm\n(%s) Tj\nET",
            $color,
            $font,
            $fontSize,
            $x,
            $y,
            $escaped,
        );
    }

    private function fillRect(float $x, float $y, float $width, float $height, string $color): string
    {
        return sprintf("%s rg\n%.2F %.2F %.2F %.2F re f", $color, $x, $y, $width, $height);
    }

    private function strokeRect(float $x, float $y, float $width, float $height, string $color): string
    {
        return sprintf("%s RG\n%.2F %.2F %.2F %.2F re S", $color, $x, $y, $width, $height);
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\(', '\)'],
            $text,
        );
    }

    /**
     * @param string[] $objects
     */
    private function compilePdf(array $objects): string
    {
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($index = 1; $index <= count($objects); $index++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
