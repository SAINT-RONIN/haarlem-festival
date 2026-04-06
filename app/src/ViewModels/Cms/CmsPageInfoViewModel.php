<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * Page metadata (title, slug, ID) displayed in the CMS page editor header.
 */
final readonly class CmsPageInfoViewModel
{
    public function __construct(
        public int    $id,
        public string $title,
        public string $slug,
    ) {}
}
