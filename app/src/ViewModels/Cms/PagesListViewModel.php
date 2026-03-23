<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS pages list.
 *
 * Carries filtered page items and search query.
 */
final readonly class PagesListViewModel
{
    /**
     * @param PageListItemViewModel[] $pages
     */
    public function __construct(
        public array  $pages,
        public string $searchQuery,
        public string $userName,
    ) {
    }
}
