<?php

declare(strict_types=1);

namespace App\Models;

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
