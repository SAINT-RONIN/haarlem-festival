<?php

declare(strict_types=1);

namespace App\Models;

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
