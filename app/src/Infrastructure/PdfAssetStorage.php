<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\DTOs\Files\StoredPdfFile;
use App\Exceptions\InvoiceGenerationException;
use App\Exceptions\TicketPdfGenerationException;
use App\Repositories\Interfaces\IMediaAssetRepository;

final class PdfAssetStorage
{
    private const TICKET_DIRECTORY_PERMISSIONS = 0775;
    private const INVOICE_DIRECTORY_PERMISSIONS = 0755;
    private const PDF_MIME_TYPE = 'application/pdf';
    private const PUBLIC_ASSET_PREFIX = '/assets/';

    public function __construct(
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    public function storeTicketPdfFile(string $fileName, string $pdfBinary): StoredPdfFile
    {
        return $this->storePdfFile(
            fileName: $fileName,
            pdfBinary: $pdfBinary,
            directoryPath: PathResolver::getTicketAssetPath(),
            relativePath: PathResolver::getTicketAssetRelativePath($fileName),
            directoryPermissions: self::TICKET_DIRECTORY_PERMISSIONS,
            directoryCreationException: new TicketPdfGenerationException('Ticket PDF directory could not be created.'),
            fileWriteException: new TicketPdfGenerationException('Ticket PDF could not be written to disk.'),
        );
    }

    public function storeInvoicePdfFile(string $fileName, string $pdfBinary): StoredPdfFile
    {
        return $this->storePdfFile(
            fileName: $fileName,
            pdfBinary: $pdfBinary,
            directoryPath: PathResolver::getInvoiceAssetPath(),
            relativePath: PathResolver::getInvoiceAssetRelativePath($fileName),
            directoryPermissions: self::INVOICE_DIRECTORY_PERMISSIONS,
            directoryCreationException: new InvoiceGenerationException('Invoice PDF directory could not be created.'),
            fileWriteException: new InvoiceGenerationException('Invoice PDF could not be written to disk.'),
        );
    }

    public function upsertPdfAsset(?int $mediaAssetId, StoredPdfFile $storedPdfFile, string $altText): int
    {
        $mediaAssetData = $this->buildPdfMediaAssetData($storedPdfFile, $altText);

        if ($mediaAssetId !== null) {
            $this->mediaAssetRepository->update($mediaAssetId, $mediaAssetData);
            return $mediaAssetId;
        }

        return $this->mediaAssetRepository->create($mediaAssetData);
    }

    public function resolveAbsolutePublicPath(string $filePath): string
    {
        if (str_starts_with($filePath, self::PUBLIC_ASSET_PREFIX)) {
            return PathResolver::getPublicPath() . $filePath;
        }

        return $filePath;
    }

    /**
     * @return array<string, int|string>
     */
    private function buildPdfMediaAssetData(StoredPdfFile $storedPdfFile, string $altText): array
    {
        return [
            'FilePath' => $storedPdfFile->relativePath,
            'OriginalFileName' => $storedPdfFile->fileName,
            'MimeType' => self::PDF_MIME_TYPE,
            'FileSizeBytes' => $storedPdfFile->fileSizeBytes,
            'AltText' => $altText,
        ];
    }

    private function storePdfFile(
        string $fileName,
        string $pdfBinary,
        string $directoryPath,
        string $relativePath,
        int $directoryPermissions,
        \RuntimeException $directoryCreationException,
        \RuntimeException $fileWriteException,
    ): StoredPdfFile {
        if (!is_dir($directoryPath) && !mkdir($directoryPath, $directoryPermissions, true) && !is_dir($directoryPath)) {
            throw $directoryCreationException;
        }

        $absolutePath = $directoryPath . '/' . $fileName;
        if (file_put_contents($absolutePath, $pdfBinary) === false) {
            throw $fileWriteException;
        }

        return new StoredPdfFile(
            fileName: $fileName,
            absolutePath: $absolutePath,
            relativePath: $relativePath,
            fileSizeBytes: $this->resolveFileSizeBytes($absolutePath),
        );
    }

    private function resolveFileSizeBytes(string $absolutePath): int
    {
        $fileSizeBytes = filesize($absolutePath);

        return $fileSizeBytes !== false ? $fileSizeBytes : 0;
    }
}
