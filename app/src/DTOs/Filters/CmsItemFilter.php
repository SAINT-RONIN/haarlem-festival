<?php

declare(strict_types=1);

namespace App\DTOs\Filters;

/**
 * Query parameters for CmsRepository item queries.
 */
final readonly class CmsItemFilter
{
    public function __construct(
        public ?int $cmsItemId = null,
        public ?int $cmsSectionId = null,
        public ?int $cmsPageId = null,
        public ?string $sectionKey = null,
    ) {
    }
}
