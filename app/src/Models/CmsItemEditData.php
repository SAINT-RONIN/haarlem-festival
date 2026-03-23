<?php

declare(strict_types=1);

namespace App\Models;

final readonly class CmsItemEditData
{
    public function __construct(
        public int $itemId,
        public string $itemKey,
        public string $displayName,
        public string $type,
        public string $typeLabel,
        public string $inputType,
        public int $maxChars,
        public string $value,
        public ?int $mediaAssetId,
        public ?CmsMediaAssetData $mediaAsset,
    ) {
    }
}
