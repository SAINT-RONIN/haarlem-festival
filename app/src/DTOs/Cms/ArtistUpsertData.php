<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Typed carrier for artist create/update form fields.
 * Extracted from POST in CmsArtistsController, validated by CmsArtistsService.
 */
final readonly class ArtistUpsertData
{
    public function __construct(
        public string $name,
        public string $style,
        public string $cardDescription,
        public string $heroSubtitle,
        public string $heroImagePath,
        public string $originText,
        public string $formedText,
        public string $bioHtml,
        public string $overviewLead,
        public string $overviewBodySecondary,
        public string $lineupHeading,
        public string $highlightsHeading,
        public string $photoGalleryHeading,
        public string $photoGalleryDescription,
        public string $albumsHeading,
        public string $albumsDescription,
        public string $listenHeading,
        public string $listenSubheading,
        public string $listenDescription,
        public string $liveCtaHeading,
        public string $liveCtaDescription,
        public string $performancesHeading,
        public string $performancesDescription,
        public int    $cardSortOrder,
        public bool   $showOnJazzOverview,
        public ?int   $imageAssetId,
        public bool   $isActive,
    ) {}
}
