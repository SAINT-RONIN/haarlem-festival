<?php

declare(strict_types=1);

namespace App\Models;

final readonly class CmsPageFilter
{
    public function __construct(
        public ?int $cmsPageId = null,
        public ?string $slug = null,
        public bool $includeLastUpdated = false,
    ) {
    }
}
