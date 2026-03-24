<?php

declare(strict_types=1);

namespace App\Models;

/**
 * All data needed to render the CMS page editor — page metadata, sections with items,
 * content/image limits. Assembled by CmsEditService.
 */
final readonly class CmsPageEditData
{
    /**
     * @param CmsSectionEditData[] $sections Enriched sections with items metadata
     */
    public function __construct(
        public CmsPage $page,
        public array $sections,
    ) {}
}
