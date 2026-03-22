<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzArtistDetailConstants;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Models\JazzArtistDetailCmsData;
use App\Models\JazzArtistDetailEvent;
use App\Models\JazzArtistDetailPageData;
use App\Repositories\CmsContentRepository;
use App\Repositories\Interfaces\IArtistAlbumRepository;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;
use App\Repositories\Interfaces\IArtistHighlightRepository;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;
use App\Repositories\Interfaces\IArtistTrackRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Services\Interfaces\IJazzArtistDetailService;

class JazzArtistDetailService implements IJazzArtistDetailService
{
    /** @var array<string, array{expiresAt:int, data:JazzArtistDetailPageData}> */
    private static array $pageCache = [];

    public function __construct(
        private readonly CmsContentRepository $cmsService,
        private readonly IEventRepository $eventRepository,
        private readonly IArtistAlbumRepository $albumRepository,
        private readonly IArtistTrackRepository $trackRepository,
        private readonly IArtistLineupMemberRepository $lineupMemberRepository,
        private readonly IArtistHighlightRepository $highlightRepository,
        private readonly IArtistGalleryImageRepository $galleryImageRepository,
    ) {
    }

    /** @throws JazzArtistDetailNotFoundException */
    public function getArtistPageDataBySlug(string $slug): JazzArtistDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $cached = $this->getCachedPageData($normalizedSlug);
        if ($cached !== null) {
            return $cached;
        }

        $event = $this->findJazzEventBySlug($normalizedSlug);

        $pageData = new JazzArtistDetailPageData(
            event: $event,
            cms: JazzArtistDetailCmsData::fromRawArray(
                $this->cmsService->getSectionContent(
                    JazzArtistDetailConstants::DETAIL_PAGE_SLUG,
                    JazzArtistDetailConstants::eventSectionKey($event->eventId),
                ),
            ),
            eventId: $event->eventId,
            albums: $this->albumRepository->findByEventId($event->eventId),
            tracks: $this->trackRepository->findByEventId($event->eventId),
            lineupMembers: $this->lineupMemberRepository->findByEventId($event->eventId),
            highlights: $this->highlightRepository->findByEventId($event->eventId),
            galleryImages: $this->galleryImageRepository->findByEventId($event->eventId),
        );

        $this->setCachedPageData($normalizedSlug, $pageData);

        return $pageData;
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

        $expiresAt = (int)($entry['expiresAt'] ?? 0);
        if ($expiresAt < time()) {
            unset(self::$pageCache[$slug]);
            return null;
        }

        $data = $entry['data'] ?? null;
        return $data instanceof JazzArtistDetailPageData ? $data : null;
    }

    /** @throws JazzArtistDetailNotFoundException */
    private function normalizeSlug(string $slug): string
    {
        $normalizedSlug = trim(strtolower(rawurldecode($slug)));
        if ($normalizedSlug === '' || str_contains($normalizedSlug, '/')) {
            throw new JazzArtistDetailNotFoundException($slug);
        }

        return trim($normalizedSlug, '-');
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
