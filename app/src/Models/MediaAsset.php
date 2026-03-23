<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `MediaAsset` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
final readonly class MediaAsset
{
    /*
     * Purpose: Stores metadata for uploaded files (images, PDFs)
     * including path, type, and accessibility info.
     */

    public function __construct(
        public int                $mediaAssetId,
        public string             $filePath,
        public string             $originalFileName,
        public string             $mimeType,
        public int                $fileSizeBytes,
        public string             $altText,
        public \DateTimeImmutable $createdAtUtc,
    ) {
    }

    /**
     * Creates a MediaAsset instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            mediaAssetId: (int)($row['MediaAssetId'] ?? throw new \InvalidArgumentException('Missing required field: MediaAssetId')),
            filePath: (string)($row['FilePath'] ?? throw new \InvalidArgumentException('Missing required field: FilePath')),
            originalFileName: (string)($row['OriginalFileName'] ?? throw new \InvalidArgumentException('Missing required field: OriginalFileName')),
            mimeType: (string)($row['MimeType'] ?? throw new \InvalidArgumentException('Missing required field: MimeType')),
            fileSizeBytes: (int)($row['FileSizeBytes'] ?? throw new \InvalidArgumentException('Missing required field: FileSizeBytes')),
            altText: (string)($row['AltText'] ?? throw new \InvalidArgumentException('Missing required field: AltText')),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: CreatedAtUtc')),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'MediaAssetId' => $this->mediaAssetId,
            'FilePath' => $this->filePath,
            'OriginalFileName' => $this->originalFileName,
            'MimeType' => $this->mimeType,
            'FileSizeBytes' => $this->fileSizeBytes,
            'AltText' => $this->altText,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
