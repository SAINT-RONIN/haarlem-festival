<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Domain\Events\ArtistDetailData;
use App\Repositories\Interfaces\IArtistAlbumRepository;
use App\Repositories\Interfaces\IArtistDetailRepository;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;
use App\Repositories\Interfaces\IArtistHighlightRepository;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;
use App\Repositories\Interfaces\IArtistTrackRepository;

// Aggregates five artist sub-entity repositories into a single detail lookup.
class ArtistDetailRepository implements IArtistDetailRepository
{
    public function __construct(
        private readonly IArtistAlbumRepository $albumRepository,
        private readonly IArtistTrackRepository $trackRepository,
        private readonly IArtistLineupMemberRepository $lineupMemberRepository,
        private readonly IArtistHighlightRepository $highlightRepository,
        private readonly IArtistGalleryImageRepository $galleryImageRepository,
    ) {}

    public function findByArtistId(int $artistId): ArtistDetailData
    {
        return new ArtistDetailData(
            albums: $this->albumRepository->findByArtistId($artistId),
            tracks: $this->trackRepository->findByArtistId($artistId),
            lineupMembers: $this->lineupMemberRepository->findByArtistId($artistId),
            highlights: $this->highlightRepository->findByArtistId($artistId),
            galleryImages: $this->galleryImageRepository->findByArtistId($artistId),
        );
    }
}
