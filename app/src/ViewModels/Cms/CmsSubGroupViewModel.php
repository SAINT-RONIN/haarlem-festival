<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A sub-group within a CMS section — groups related items under a heading (e.g., 'Hero', 'Pricing').
 */
final readonly class CmsSubGroupViewModel
{
    /**
     * @param CmsItemDisplayViewModel[] $items
     */
    public function __construct(
        public string $label,
        public string $icon,
        public string $color,
        public int    $columns,
        public array  $items,
    ) {}
}
