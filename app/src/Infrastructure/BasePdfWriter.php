<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Shared PDF 1.4 primitives for generating lightweight single-page PDF documents.
 *
 * Provides low-level drawing commands (text, rectangles, wrapped text) and a
 * compilePdf() method that assembles raw PDF objects into a valid document.
 * Subclasses implement domain-specific page layouts (tickets, invoices, etc.).
 */
abstract class BasePdfWriter
{
    protected function writeText(
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

    protected function fillRect(float $x, float $y, float $width, float $height, string $color): string
    {
        return sprintf("%s rg\n%.2F %.2F %.2F %.2F re f", $color, $x, $y, $width, $height);
    }

    protected function strokeRect(float $x, float $y, float $width, float $height, string $color): string
    {
        return sprintf("%s RG\n%.2F %.2F %.2F %.2F re S", $color, $x, $y, $width, $height);
    }

    protected function escapePdfText(string $text): string
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
    protected function compilePdf(array $objects): string
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

    /**
     * @return string[]
     */
    protected function wrapText(string $text, int $maxCharsPerLine): array
    {
        $wrapped = wordwrap($text, $maxCharsPerLine, "\n", true);
        return explode("\n", $wrapped);
    }

    protected function writeWrappedText(
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

    protected function writeSectionLabel(string $text, float $x, float $y): string
    {
        return $this->writeText(strtoupper($text), $x, $y, 10, true, '0.49 0.55 0.64');
    }
}
