<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Read-only projection of a single ticket for the CMS order detail page.
 */
final readonly class CmsOrderTicketDto
{
    public function __construct(
        public int     $ticketId,
        public string  $ticketCode,
        public bool    $isScanned,
        public ?string $scannedAtUtc,
        public ?string $scannedByUserName,
        public ?string $pdfAssetPath,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            ticketId:         (int) $row['TicketId'],
            ticketCode:       (string) $row['TicketCode'],
            isScanned:        (bool) ($row['IsScanned'] ?? false),
            scannedAtUtc:     isset($row['ScannedAtUtc']) ? (string) $row['ScannedAtUtc'] : null,
            scannedByUserName:isset($row['ScannedByUserName']) ? (string) $row['ScannedByUserName'] : null,
            pdfAssetPath:     isset($row['PdfAssetPath']) ? (string) $row['PdfAssetPath'] : null,
        );
    }
}
