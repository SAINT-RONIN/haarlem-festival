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

    /**
     * Returns a copy of this object with Jazz Overview flags applied.
     *
     * Used when the CMS "Create Artist" page is opened from the Jazz Overview card list,
     * pre-selecting the Jazz Overview checkbox and setting the initial sort order so the
     * new artist lands in the right position without manual adjustment.
     *
     * All other fields are preserved from the original object — only $showOnJazzOverview
     * and $cardSortOrder are changed.
     *
     * @param int $sortOrder The initial card sort position (0 = no preference; >0 = explicit slot).
     * @return self A new instance with showOnJazzOverview = true and the given sort order.
     */
    public function withJazzOverview(int $sortOrder): self
    {
        return new self(
            name: $this->name,
            style: $this->style,
            cardDescription: $this->cardDescription,
            heroSubtitle: $this->heroSubtitle,
            heroImagePath: $this->heroImagePath,
            originText: $this->originText,
            formedText: $this->formedText,
            bioHtml: $this->bioHtml,
            overviewLead: $this->overviewLead,
            overviewBodySecondary: $this->overviewBodySecondary,
            lineupHeading: $this->lineupHeading,
            highlightsHeading: $this->highlightsHeading,
            photoGalleryHeading: $this->photoGalleryHeading,
            photoGalleryDescription: $this->photoGalleryDescription,
            albumsHeading: $this->albumsHeading,
            albumsDescription: $this->albumsDescription,
            listenHeading: $this->listenHeading,
            listenSubheading: $this->listenSubheading,
            listenDescription: $this->listenDescription,
            liveCtaHeading: $this->liveCtaHeading,
            liveCtaDescription: $this->liveCtaDescription,
            performancesHeading: $this->performancesHeading,
            performancesDescription: $this->performancesDescription,
            cardSortOrder: $sortOrder,
            showOnJazzOverview: true,
            imageAssetId: $this->imageAssetId,
            isActive: $this->isActive,
        );
    }
}
