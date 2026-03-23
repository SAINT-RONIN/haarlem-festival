<?php

declare(strict_types=1);

namespace App\Models;

final readonly class CmsSectionFilter
{
    public function __construct(
        public ?int $cmsPageId = null,
        public ?string $sectionKey = null,
    ) {
    }
}
