<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

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
