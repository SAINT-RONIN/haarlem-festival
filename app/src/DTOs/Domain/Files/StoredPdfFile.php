<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Files;

final readonly class StoredPdfFile
{
    public function __construct(
        public string $fileName,
        public string $absolutePath,
        public string $relativePath,
        public int $fileSizeBytes,
    ) {
    }
}
