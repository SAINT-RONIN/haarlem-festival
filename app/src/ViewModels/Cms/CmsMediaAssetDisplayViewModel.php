<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A media asset preview in the CMS page editor — thumbnail, file name, and asset ID.
 */
final readonly class CmsMediaAssetDisplayViewModel
{
    public function __construct(
        public string $filePath,
        public string $originalFileName,
        public string $altText,
    ) {}
}
