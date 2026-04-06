<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Filters;

/**
 * Query parameters for CmsRepository section queries.
 */
final readonly class CmsSectionFilter
{
    public function __construct(
        public ?int $cmsPageId = null,
        public ?string $sectionKey = null,
    ) {
    }
}
