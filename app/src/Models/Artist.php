<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the Artist table.
 *
 * Artists are performers associated with Jazz events, displayed on both the jazz landing
 * page and individual artist detail pages.
 */
final readonly class Artist
{
    public function __construct(
        public int                $artistId,
        public string             $name,
        public string             $style,
        public string             $cardDescription,
        public string             $heroSubtitle,
        public string             $heroImagePath,
        public string             $originText,
        public string             $formedText,
        public string             $bioHtml,
        public string             $overviewLead,
        public string             $overviewBodySecondary,
        public string             $lineupHeading,
        public string             $highlightsHeading,
        public string             $photoGalleryHeading,
        public string             $photoGalleryDescription,
        public string             $albumsHeading,
        public string             $albumsDescription,
        public string             $listenHeading,
        public string             $listenSubheading,
        public string             $listenDescription,
        public string             $liveCtaHeading,
        public string             $liveCtaDescription,
        public string             $performancesHeading,
        public string             $performancesDescription,
        public int                $cardSortOrder,
        public bool               $showOnJazzOverview,
        public ?int               $imageAssetId,
        public ?string            $imagePath,
        public bool               $isActive,
        public \DateTimeImmutable $createdAtUtc,
    ) {}

    /**
     * Creates an Artist instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            artistId: (int) ($row['ArtistId'] ?? throw new \InvalidArgumentException('Missing required field: ArtistId')),
            name: (string) ($row['Name'] ?? throw new \InvalidArgumentException('Missing required field: Name')),
            style: (string) ($row['Style'] ?? throw new \InvalidArgumentException('Missing required field: Style')),
            cardDescription: (string) ($row['CardDescription'] ?? ''),
            heroSubtitle: (string) ($row['HeroSubtitle'] ?? ''),
            heroImagePath: (string) ($row['HeroImagePath'] ?? ''),
            originText: (string) ($row['OriginText'] ?? ''),
            formedText: (string) ($row['FormedText'] ?? ''),
            bioHtml: (string) ($row['BioHtml'] ?? throw new \InvalidArgumentException('Missing required field: BioHtml')),
            overviewLead: (string) ($row['OverviewLead'] ?? ''),
            overviewBodySecondary: (string) ($row['OverviewBodySecondary'] ?? ''),
            lineupHeading: (string) ($row['LineupHeading'] ?? ''),
            highlightsHeading: (string) ($row['HighlightsHeading'] ?? ''),
            photoGalleryHeading: (string) ($row['PhotoGalleryHeading'] ?? ''),
            photoGalleryDescription: (string) ($row['PhotoGalleryDescription'] ?? ''),
            albumsHeading: (string) ($row['AlbumsHeading'] ?? ''),
            albumsDescription: (string) ($row['AlbumsDescription'] ?? ''),
            listenHeading: (string) ($row['ListenHeading'] ?? ''),
            listenSubheading: (string) ($row['ListenSubheading'] ?? ''),
            listenDescription: (string) ($row['ListenDescription'] ?? ''),
            liveCtaHeading: (string) ($row['LiveCtaHeading'] ?? ''),
            liveCtaDescription: (string) ($row['LiveCtaDescription'] ?? ''),
            performancesHeading: (string) ($row['PerformancesHeading'] ?? ''),
            performancesDescription: (string) ($row['PerformancesDescription'] ?? ''),
            cardSortOrder: (int) ($row['CardSortOrder'] ?? 0),
            showOnJazzOverview: (bool) ($row['ShowOnJazzOverview'] ?? false),
            imageAssetId: isset($row['ImageAssetId']) ? (int) $row['ImageAssetId'] : null,
            imagePath: isset($row['ImagePath']) ? (string) $row['ImagePath'] : null,
            isActive: (bool) ($row['IsActive'] ?? throw new \InvalidArgumentException('Missing required field: IsActive')),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: CreatedAtUtc')),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ArtistId' => $this->artistId,
            'Name' => $this->name,
            'Style' => $this->style,
            'CardDescription' => $this->cardDescription,
            'HeroSubtitle' => $this->heroSubtitle,
            'HeroImagePath' => $this->heroImagePath,
            'OriginText' => $this->originText,
            'FormedText' => $this->formedText,
            'BioHtml' => $this->bioHtml,
            'OverviewLead' => $this->overviewLead,
            'OverviewBodySecondary' => $this->overviewBodySecondary,
            'LineupHeading' => $this->lineupHeading,
            'HighlightsHeading' => $this->highlightsHeading,
            'PhotoGalleryHeading' => $this->photoGalleryHeading,
            'PhotoGalleryDescription' => $this->photoGalleryDescription,
            'AlbumsHeading' => $this->albumsHeading,
            'AlbumsDescription' => $this->albumsDescription,
            'ListenHeading' => $this->listenHeading,
            'ListenSubheading' => $this->listenSubheading,
            'ListenDescription' => $this->listenDescription,
            'LiveCtaHeading' => $this->liveCtaHeading,
            'LiveCtaDescription' => $this->liveCtaDescription,
            'PerformancesHeading' => $this->performancesHeading,
            'PerformancesDescription' => $this->performancesDescription,
            'CardSortOrder' => $this->cardSortOrder,
            'ShowOnJazzOverview' => $this->showOnJazzOverview,
            'ImageAssetId' => $this->imageAssetId,
            'ImagePath' => $this->imagePath,
            'IsActive' => $this->isActive,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
