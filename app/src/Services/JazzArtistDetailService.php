<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzArtistDetailConstants;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Helpers\SlugHelper;
use App\Models\Artist;
use App\DTOs\Domain\Events\JazzArtistDetailEvent;
use App\DTOs\Domain\Pages\JazzArtistDetailPageData;
use App\Repositories\Interfaces\IArtistDetailRepository;
use App\Repositories\Interfaces\IArtistRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Services\Interfaces\IJazzArtistDetailService;

/**
 * Assembles the full detail-page payload for a single Jazz artist.
 *
 * Results are cached in-memory with a configurable TTL to avoid
 * redundant queries within the same request cycle.
 */
class JazzArtistDetailService implements IJazzArtistDetailService
{
    /** @var array<string, array{expiresAt:int, data:JazzArtistDetailPageData}> */
    private static array $pageCache = [];

    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IArtistRepository $artistRepository,
        private readonly IArtistDetailRepository $artistDetailRepository,
    ) {}

    /** @throws JazzArtistDetailNotFoundException */
    public function getArtistPageDataBySlug(string $slug): JazzArtistDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $cached = $this->getCachedPageData($normalizedSlug);
        if ($cached !== null) {
            return $cached;
        }

        $event = $this->findJazzEventBySlug($normalizedSlug);
        $pageData = $this->buildPageData($event);
        $this->setCachedPageData($normalizedSlug, $pageData);

        return $pageData;
    }

    private function buildPageData(JazzArtistDetailEvent $event): JazzArtistDetailPageData
    {
        $artist = $this->findArtistForEvent($event);
        $artistDetail = $this->artistDetailRepository->findByArtistId($artist->artistId);

        return new JazzArtistDetailPageData(
            event: $event,
            artist: $artist,
            eventId: $event->eventId,
            albums: $artistDetail->albums,
            tracks: $artistDetail->tracks,
            lineupMembers: $artistDetail->lineupMembers,
            highlights: $artistDetail->highlights,
            galleryImages: $artistDetail->galleryImages,
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

        if ((int) ($entry['expiresAt'] ?? 0) < time()) {
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

    /** @throws JazzArtistDetailNotFoundException */
    private function findArtistForEvent(JazzArtistDetailEvent $event): Artist
    {
        if ($event->artistId === null) {
            throw new JazzArtistDetailNotFoundException($event->slug);
        }

        $artist = $this->artistRepository->findById($event->artistId);
        if ($artist === null || !$artist->isActive) {
            throw new JazzArtistDetailNotFoundException($event->slug);
        }

        return $artist;
    }
}
