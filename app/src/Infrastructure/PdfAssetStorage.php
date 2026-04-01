<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\DTOs\Files\StoredPdfFile;
use App\Exceptions\InvoiceGenerationException;
use App\Exceptions\TicketPdfGenerationException;
use App\Repositories\Interfaces\IMediaAssetRepository;

/**
 * Stores generated PDF files on disk and keeps their matching MediaAsset records in sync.
 *
 * This helper exists so services like ticket and invoice fulfillment do not need to duplicate
 * file-writing, path-resolution, and media-asset persistence logic.
 */
final class PdfAssetStorage
{
    private const TICKET_DIRECTORY_PERMISSIONS = 0775;
    private const INVOICE_DIRECTORY_PERMISSIONS = 0755;
    private const PDF_MIME_TYPE = 'application/pdf';
    private const PUBLIC_ASSET_PREFIX = '/assets/';

    /**
     * Stores the media repository used to create or update PDF asset records.
     *
     * The constructor returns nothing because this class only needs one collaborator:
     * the repository that persists the metadata for files written to disk.
     */
    public function __construct(
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * Writes a generated ticket PDF to disk and returns its stored file details.
     *
     * The returned StoredPdfFile contains both filesystem and public-path information
     * because later steps need to attach the file and persist metadata about it.
     */
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

    /**
     * Writes a generated invoice PDF to disk and returns its stored file details.
     *
     * It mirrors ticket storage so both invoice and ticket flows can rely on the same
     * storage contract even though they use different directories and exception types.
     */
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

    /**
     * Returns the MediaAsset id for the given stored PDF file.
     *
     * A new id is returned when no asset exists yet; the existing id is returned when
     * the asset is updated, which keeps the calling service's relationship stable.
     */
    public function upsertPdfAsset(?int $mediaAssetId, StoredPdfFile $storedPdfFile, string $altText): int
    {
        $mediaAssetData = $this->buildPdfMediaAssetData($storedPdfFile, $altText);

        if ($mediaAssetId !== null) {
            // Reuse the same asset record when the PDF is regenerated for an existing entity.
            $this->mediaAssetRepository->update($mediaAssetId, $mediaAssetData);
            return $mediaAssetId;
        }

        return $this->mediaAssetRepository->create($mediaAssetData);
    }

    /**
     * Returns an absolute filesystem path for a stored asset path.
     *
     * Public asset paths are converted because email attachments and file checks
     * need a real disk location, not a browser-facing URL fragment.
     */
    public function resolveAbsolutePublicPath(string $filePath): string
    {
        if (str_starts_with($filePath, self::PUBLIC_ASSET_PREFIX)) {
            return PathResolver::getPublicPath() . $filePath;
        }

        return $filePath;
    }

    /**
     * Builds the array shape expected by the MediaAsset repository for PDF files.
     * The returned array is intentionally repository-ready so callers do not need
     * to know the MediaAsset table column names.
     *
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

    /**
     * Writes one PDF file to the target directory and returns metadata about the stored file.
     *
     * The returned StoredPdfFile is used by callers because they usually need both the
     * absolute path for immediate work and the relative path for database storage.
     */
    private function storePdfFile(
        string $fileName,
        string $pdfBinary,
        string $directoryPath,
        string $relativePath,
        int $directoryPermissions,
        \RuntimeException $directoryCreationException,
        \RuntimeException $fileWriteException,
    ): StoredPdfFile {
        // The double is_dir check avoids race-condition issues if another request creates the folder first.
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

    /**
     * Returns the stored file size in bytes, or zero when PHP cannot read it.
     *
     * Zero is used as a safe fallback because missing file-size metadata should not crash
     * fulfillment after the file itself was already written successfully.
     */
    private function resolveFileSizeBytes(string $absolutePath): int
    {
        $fileSizeBytes = filesize($absolutePath);

        return $fileSizeBytes !== false ? $fileSizeBytes : 0;
    }
}
