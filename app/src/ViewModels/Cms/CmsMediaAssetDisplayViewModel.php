<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsMediaAssetDisplayViewModel
{
    public function __construct(
        public string $filePath,
        public string $originalFileName,
        public string $altText,
    ) {}
}
