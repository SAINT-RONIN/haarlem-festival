<?php

declare(strict_types=1);

namespace App\Models;

final readonly class CmsMediaAssetData
{
    public function __construct(
        public string $filePath,
        public string $originalFileName,
        public string $altText,
    ) {}
}
