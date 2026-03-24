<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS artists list page (artists.php).
 */
final readonly class CmsArtistsListViewModel
{
    /**
     * @param CmsArtistListItemViewModel[] $items
     */
    public function __construct(
        public array   $items,
        public string  $searchQuery,
        public ?string $successMessage,
        public ?string $errorMessage,
        public string  $deleteCsrfToken,
    ) {}
}
