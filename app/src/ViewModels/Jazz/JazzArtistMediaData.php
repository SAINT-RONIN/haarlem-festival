<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Media section for a jazz artist detail page — gallery images and video embed.
 */
final readonly class JazzArtistMediaData
{
    /**
     * @param JazzArtistAlbumData[] $albums
     * @param JazzArtistTrackData[] $tracks
     */
    public function __construct(
        public string $albumsHeading,
        public string $albumsDescription,
        public array $albums,
        public string $listenHeading,
        public string $listenSubheading,
        public string $listenDescription,
        public string $listenPlayButtonLabel,
        public string $listenPlayExcerptText,
        public string $listenTrackArtworkAltSuffix,
        public array $tracks,
    ) {}
}
