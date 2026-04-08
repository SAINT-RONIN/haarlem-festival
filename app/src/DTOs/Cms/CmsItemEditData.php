<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * A single CMS item in its editable form — includes display name, type label,
 * value, and validation constraints.
 */
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
    ) {}
}
