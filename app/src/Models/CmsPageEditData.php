<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed result returned by CmsEditService::getPageForEditing().
 * Carries the page model and enriched section data for the CMS editor view.
 */
final readonly class CmsPageEditData
{
    /**
     * @param array<int, array<string, mixed>> $sections Enriched sections with items metadata
     */
    public function __construct(
        public CmsPage $page,
        public array $sections,
    ) {}
}
