<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single page row in the CMS pages list table.
 */
final readonly class PageListItemViewModel
{
    public function __construct(
        public int    $id,
        public string $title,
        public string $slug,
        public string $status,
        public string $updatedAt,
    ) {}
}
