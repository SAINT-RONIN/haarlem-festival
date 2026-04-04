<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsJazzLineupManagerViewModel
{
    /**
     * @param CmsJazzLineupCardViewModel[] $cards
     * @param CmsJazzLineupAvailableArtistViewModel[] $availableArtists
     */
    public function __construct(
        public string $sectionKey,
        public string $returnTo,
        public string $createCardUrl,
        public string $addCsrfToken,
        public string $removeCsrfToken,
        public array $cards,
        public array $availableArtists,
    ) {}
}
