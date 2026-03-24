<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * Pre-built sort column header for CMS list pages.
 *
 * Carries the toggle URL and current sort direction icon.
 */
final readonly class CmsSortColumnViewModel
{
    public function __construct(
        public string $url,
        public string $icon,
    ) {
    }
}
