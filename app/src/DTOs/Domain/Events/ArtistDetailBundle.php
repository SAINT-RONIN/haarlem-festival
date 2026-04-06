<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

use App\Models\ArtistAlbum;
use App\Models\ArtistGalleryImage;
use App\Models\ArtistHighlight;
use App\Models\ArtistLineupMember;
use App\Models\ArtistTrack;

/**
 * Aggregated artist detail data: albums, tracks, lineup members, highlights, and gallery images.
 */
final readonly class ArtistDetailBundle
{
    /**
     * @param ArtistAlbum[] $albums
     * @param ArtistTrack[] $tracks
     * @param ArtistLineupMember[] $lineupMembers
     * @param ArtistHighlight[] $highlights
     * @param ArtistGalleryImage[] $galleryImages
     */
    public function __construct(
        public array $albums,
        public array $tracks,
        public array $lineupMembers,
        public array $highlights,
        public array $galleryImages,
    ) {
    }
}
