<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single editable CMS item in the page editor — field label, current value, type, and constraints.
 */
final readonly class CmsItemDisplayViewModel
{
    public function __construct(
        public int                            $itemId,
        public string                         $itemKey,
        public string                         $displayName,
        public string                         $type,
        public string                         $typeLabel,
        public string                         $inputType,
        public int                            $maxChars,
        public string                         $value,
        public ?int                           $mediaAssetId,
        public ?CmsMediaAssetDisplayViewModel $mediaAsset,
        public bool                           $isTextarea,
    ) {}
}
