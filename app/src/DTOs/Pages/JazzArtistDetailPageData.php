<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

/**
 * All data for a single jazz artist detail page — CMS content, albums, tracks,
 * lineup, gallery. Assembled by JazzArtistDetailService.
 */
final readonly class JazzArtistDetailPageData
{
    /**
     * @param ArtistAlbum[] $albums
     * @param ArtistTrack[] $tracks
     * @param ArtistLineupMember[] $lineupMembers
     * @param ArtistHighlight[] $highlights
     * @param ArtistGalleryImage[] $galleryImages
     */
    public function __construct(
        public JazzArtistDetailEvent $event,
        public JazzArtistDetailCmsData $cms,
        public int $eventId,
        public array $albums = [],
        public array $tracks = [],
        public array $lineupMembers = [],
        public array $highlights = [],
        public array $galleryImages = [],
    ) {
    }
}
