<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\DTOs\Events\JazzArtistDetailEvent;
use App\Models\Artist;

/**
 * All data for a single jazz artist detail page — event data, artist profile,
 * and artist-owned collections. Assembled by JazzArtistDetailService.
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
        public Artist $artist,
        public int $eventId,
        public array $albums = [],
        public array $tracks = [],
        public array $lineupMembers = [],
        public array $highlights = [],
        public array $galleryImages = [],
    ) {
    }
}
