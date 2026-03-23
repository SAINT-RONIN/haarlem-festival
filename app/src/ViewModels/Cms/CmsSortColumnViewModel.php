<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * Pre-built sort column data for a sortable table header.
 */
final readonly class CmsSortColumnViewModel
{
    public function __construct(
        public string $url,
        public string $icon,
    ) {
    }
}
