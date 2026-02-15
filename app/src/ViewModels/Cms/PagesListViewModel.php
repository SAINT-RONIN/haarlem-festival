<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS Pages list view.
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
    )
    {
    }
}

