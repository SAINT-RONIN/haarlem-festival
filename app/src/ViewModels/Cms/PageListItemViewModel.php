<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a page item in the CMS pages list.
 */
final readonly class PageListItemViewModel
{
    public function __construct(
        public int    $id,
        public string $title,
        public string $slug,
        public string $status,
        public string $updatedAt,
    ) {
    }
}
