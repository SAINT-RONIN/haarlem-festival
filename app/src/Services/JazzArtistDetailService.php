<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzArtistDetailConstants;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Helpers\SlugHelper;
use App\Models\JazzArtistDetailCmsData;
use App\Models\JazzArtistDetailEvent;
use App\Models\JazzArtistDetailPageData;
use App\Repositories\JazzContentRepository;
use App\Repositories\Interfaces\IArtistAlbumRepository;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;
use App\Repositories\Interfaces\IArtistHighlightRepository;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;
use App\Repositories\Interfaces\IArtistTrackRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Services\Interfaces\IJazzArtistDetailService;

/**
 * Assembles the full detail-page payload for a single Jazz artist.
 *
 * Combines event data, CMS overrides, albums, tracks, lineup members,
 * highlights, and gallery images from six different repositories.
 * Results are cached in-memory with a configurable TTL to avoid
 * redundant queries within the same request cycle.
 */
class JazzArtistDetailService implements IJazzArtistDetailService
{
    /** @var array<string, array{expiresAt:int, data:JazzArtistDetailPageData}> */
    private static array $pageCache = [];

    public function __construct(
        private readonly JazzContentRepository $jazzContentRepo,
        private readonly IEventRepository $eventRepository,
        private readonly IArtistAlbumRepository $albumRepository,
        private readonly IArtistTrackRepository $trackRepository,
        private readonly IArtistLineupMemberRepository $lineupMemberRepository,
        private readonly IArtistHighlightRepository $highlightRepository,
        private readonly IArtistGalleryImageRepository $galleryImageRepository,
    ) {
    }

    /**
     * Returns the complete artist detail page payload for a given URL slug.
     *
     * Normalises the slug, checks the in-memory cache, and if missing
     * resolves the event then aggregates CMS + repository data into a
     * single JazzArtistDetailPageData object.
     *
     * @throws JazzArtistDetailNotFoundException if the slug is invalid or no matching active Jazz event exists
     */
    public function getArtistPageDataBySlug(string $slug): JazzArtistDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $cached = $this->getCachedPageData($normalizedSlug);
        if ($cached !== null) {
            return $cached;
        }

        // Resolve the event from the database and build the full page payload
        $event = $this->findJazzEventBySlug($normalizedSlug);
        $pageData = $this->buildPageData($event);
        $this->setCachedPageData($normalizedSlug, $pageData);

        return $pageData;
    }

    private function fetchCmsContent(int $eventId): JazzArtistDetailCmsData
    {
        return $this->jazzContentRepo->findArtistDetailCmsData(
            JazzArtistDetailConstants::DETAIL_PAGE_SLUG,
            JazzArtistDetailConstants::eventSectionKey($eventId),
        );
    }

    /**
     * Aggregates CMS content and all artist-related collections (albums, tracks,
     * lineup, highlights, gallery) into a single page-data object.
     */
    private function buildPageData(JazzArtistDetailEvent $event): JazzArtistDetailPageData
    {
        return new JazzArtistDetailPageData(
            event: $event,
            cms: $this->fetchCmsContent($event->eventId),
            eventId: $event->eventId,
            albums: $this->albumRepository->findByEventId($event->eventId),
            tracks: $this->trackRepository->findByEventId($event->eventId),
            lineupMembers: $this->lineupMemberRepository->findByEventId($event->eventId),
            highlights: $this->highlightRepository->findByEventId($event->eventId),
            galleryImages: $this->galleryImageRepository->findByEventId($event->eventId),
        );
    }

    private function setCachedPageData(string $slug, JazzArtistDetailPageData $pageData): void
    {
        self::$pageCache[$slug] = [
            'expiresAt' => time() + JazzArtistDetailConstants::PAGE_CACHE_TTL_SECONDS,
            'data' => $pageData,
        ];
    }

    private function getCachedPageData(string $slug): ?JazzArtistDetailPageData
    {
        $entry = self::$pageCache[$slug] ?? null;
        if (!is_array($entry)) {
            return null;
        }

        if ((int)($entry['expiresAt'] ?? 0) < time()) {
            unset(self::$pageCache[$slug]);
            return null;
        }

        $data = $entry['data'] ?? null;
        return $data instanceof JazzArtistDetailPageData ? $data : null;
    }

    /** @throws JazzArtistDetailNotFoundException */
    private function normalizeSlug(string $slug): string
    {
        return SlugHelper::normalize($slug) ?? throw new JazzArtistDetailNotFoundException($slug);
    }

    /** @throws JazzArtistDetailNotFoundException */
    private function findJazzEventBySlug(string $slug): JazzArtistDetailEvent
    {
        $event = $this->eventRepository->findActiveJazzBySlug($slug);
        if ($event === null) {
            throw new JazzArtistDetailNotFoundException($slug);
        }

        return $event;
    }
}
