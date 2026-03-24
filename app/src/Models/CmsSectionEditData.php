<?php

declare(strict_types=1);

namespace App\Models;

/**
 * A single CMS section with its editable items, ready for the page editor form.
 */
final readonly class CmsSectionEditData
{
    /**
     * @param CmsItemEditData[] $items
     */
    public function __construct(
        public int $sectionId,
        public string $sectionKey,
        public string $displayName,
        public array $items,
    ) {}
}
