<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsJazzLineupCardFormViewModel
{
    /**
     * @param CmsArtistOptionViewModel[] $artists
     * @param array<string, string> $errors
     */
    public function __construct(
        public ?int $artistId,
        public string $name,
        public string $style,
        public string $cardDescription,
        public ?int $imageAssetId,
        public string $imageUrl,
        public int $cardSortOrder,
        public bool $isActive,
        public array $artists,
        public string $csrfToken,
        public string $formAction,
        public string $pageTitle,
        public string $returnTo,
        public string $backUrl,
        public array $errors,
    ) {}
}
