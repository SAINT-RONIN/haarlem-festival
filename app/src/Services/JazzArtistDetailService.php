<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Repositories\EventRepository;

class JazzArtistDetailService
{
    private const DETAIL_PAGE_SLUG = 'jazz-artist-detail';
    private const SCHEDULE_MAX_DAYS = 7;
    private const MAX_ALBUMS = 8;
    private const MAX_TRACKS = 12;
    private const MAX_LIST_ITEMS = 24;
    private const SLUG_PATTERN = '/^[a-z0-9-]+$/';

    public function __construct(
        private readonly CmsService $cmsService,
        private readonly ScheduleService $scheduleService,
        private readonly EventRepository $eventRepository,
    ) {
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    public function getArtistPageDataBySlug(string $slug): array
    {
        $normalizedSlug = $this->normalizeAndValidateSlug($slug);
        $event = $this->findJazzEventBySlug($normalizedSlug);
        $eventId = (int)$event['EventId'];
        $cms = $this->cmsService->getSectionContent(self::DETAIL_PAGE_SLUG, 'event_' . $eventId);

        return [
            ...$this->buildHeroData($event, $cms),
            ...$this->buildOverviewData($event, $cms),
            ...$this->buildSectionsData($cms),
            'performances' => $this->buildPerformances($eventId),
        ];
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    private function normalizeAndValidateSlug(string $slug): string
    {
        $normalizedSlug = trim(strtolower($slug));

        if ($normalizedSlug === '' || preg_match(self::SLUG_PATTERN, $normalizedSlug) !== 1) {
            throw new JazzArtistDetailNotFoundException($slug);
        }

        return $normalizedSlug;
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    private function findJazzEventBySlug(string $slug): array
    {
        $events = $this->eventRepository->findEvents([
            'eventTypeId' => EventTypeId::Jazz->value,
            'isActive' => true,
        ]);

        foreach ($events as $event) {
            $eventTitle = (string)($event['Title'] ?? '');
            if ($this->toSlug($eventTitle) === $slug) {
                return $event;
            }
        }

        throw new JazzArtistDetailNotFoundException($slug);
    }

    private function buildHeroData(array $event, array $cms): array
    {
        $eventTitle = (string)($event['Title'] ?? '');

        return [
            'heroTitle' => $eventTitle,
            'heroSubtitle' => $this->coalesce($this->cmsValue($cms, 'hero_subtitle'), (string)($event['ShortDescription'] ?? '')),
            'heroBackgroundImageUrl' => $this->cmsValue($cms, 'hero_background_image'),
            'originText' => $this->cmsValue($cms, 'origin_text'),
            'formedText' => $this->cmsValue($cms, 'formed_text'),
            'performancesText' => $this->cmsValue($cms, 'performances_text'),
            'heroBackButtonText' => $this->cmsValue($cms, 'hero_back_button_text'),
            'heroBackButtonUrl' => $this->cmsValue($cms, 'hero_back_button_url'),
            'heroReserveButtonText' => $this->cmsValue($cms, 'hero_reserve_button_text'),
        ];
    }

    private function buildOverviewData(array $event, array $cms): array
    {
        return [
            'overviewHeading' => $this->coalesce($this->cmsValue($cms, 'overview_heading'), (string)($event['Title'] ?? '')),
            'overviewLead' => $this->coalesce($this->cmsValue($cms, 'overview_lead'), (string)($event['ShortDescription'] ?? '')),
            'overviewBodyPrimary' => $this->coalesce($this->cmsValue($cms, 'overview_body_primary'), $this->buildPrimaryOverviewFallback($event)),
            'overviewBodySecondary' => $this->cmsValue($cms, 'overview_body_secondary'),
        ];
    }

    private function buildSectionsData(array $cms): array
    {
        return [
            'lineupHeading' => $this->cmsValue($cms, 'lineup_heading'),
            'lineup' => $this->collectTextList($cms, 'lineup_', self::MAX_LIST_ITEMS),
            'highlightsHeading' => $this->cmsValue($cms, 'highlights_heading'),
            'highlights' => $this->collectTextList($cms, 'highlight_', self::MAX_LIST_ITEMS),
            'photoGalleryHeading' => $this->cmsValue($cms, 'photo_gallery_heading'),
            'photoGalleryDescription' => $this->cmsValue($cms, 'photo_gallery_description'),
            'galleryImages' => $this->collectTextList($cms, 'gallery_image_', self::MAX_LIST_ITEMS),
            'albumsHeading' => $this->cmsValue($cms, 'albums_heading'),
            'albumsDescription' => $this->cmsValue($cms, 'albums_description'),
            'albums' => $this->buildAlbums($cms),
            'listenHeading' => $this->cmsValue($cms, 'listen_heading'),
            'listenSubheading' => $this->cmsValue($cms, 'listen_subheading'),
            'listenDescription' => $this->cmsValue($cms, 'listen_description'),
            'listenPlayButtonLabel' => $this->cmsValue($cms, 'listen_play_button_label'),
            'listenPlayExcerptText' => $this->cmsValue($cms, 'listen_play_excerpt_text'),
            'listenTrackArtworkAltSuffix' => $this->cmsValue($cms, 'listen_track_artwork_alt_suffix'),
            'tracks' => $this->buildTracks($cms),
            'liveCtaHeading' => $this->cmsValue($cms, 'live_cta_heading'),
            'liveCtaDescription' => $this->cmsValue($cms, 'live_cta_description'),
            'liveCtaBookButtonText' => $this->cmsValue($cms, 'live_cta_book_button_text'),
            'liveCtaScheduleButtonText' => $this->cmsValue($cms, 'live_cta_schedule_button_text'),
            'liveCtaScheduleButtonUrl' => $this->cmsValue($cms, 'live_cta_schedule_button_url'),
            'performancesSectionId' => $this->cmsValue($cms, 'performances_section_id'),
            'performancesHeading' => $this->cmsValue($cms, 'performances_heading'),
            'performancesDescription' => $this->cmsValue($cms, 'performances_description'),
        ];
    }

    private function buildPerformances(int $eventId): array
    {
        $scheduleData = $this->scheduleService->getScheduleData('jazz', EventTypeId::Jazz->value, self::SCHEDULE_MAX_DAYS, $eventId);
        $performances = [];

        foreach ($scheduleData['days'] ?? [] as $day) {
            foreach ($day['events'] ?? [] as $event) {
                $performances[] = $event;
            }
        }

        return $performances;
    }

    private function buildAlbums(array $cms): array
    {
        $albums = [];

        for ($index = 1; $index <= self::MAX_ALBUMS; $index++) {
            $title = $this->cmsValue($cms, 'album_' . $index . '_title');
            if ($title === '') {
                continue;
            }

            $albums[] = [
                'title' => $title,
                'description' => $this->cmsValue($cms, 'album_' . $index . '_description'),
                'year' => $this->cmsValue($cms, 'album_' . $index . '_year'),
                'tag' => $this->cmsValue($cms, 'album_' . $index . '_tag'),
                'imageUrl' => $this->cmsValue($cms, 'album_' . $index . '_image'),
            ];
        }

        return $albums;
    }

    private function buildTracks(array $cms): array
    {
        $tracks = [];

        for ($index = 1; $index <= self::MAX_TRACKS; $index++) {
            $title = $this->cmsValue($cms, 'track_' . $index . '_title');
            if ($title === '') {
                continue;
            }

            $tracks[] = [
                'title' => $title,
                'album' => $this->cmsValue($cms, 'track_' . $index . '_album'),
                'description' => $this->cmsValue($cms, 'track_' . $index . '_description'),
                'duration' => $this->cmsValue($cms, 'track_' . $index . '_duration'),
                'imageUrl' => $this->cmsValue($cms, 'track_' . $index . '_image'),
                'progressClass' => $this->cmsValue($cms, 'track_' . $index . '_progress_class'),
            ];
        }

        return $tracks;
    }

    private function collectTextList(array $cms, string $prefix, int $maxItems): array
    {
        $values = [];

        for ($index = 1; $index <= $maxItems; $index++) {
            $value = $this->cmsValue($cms, $prefix . $index);
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }

    private function buildPrimaryOverviewFallback(array $event): string
    {
        $descriptionHtml = (string)($event['LongDescriptionHtml'] ?? '');
        if ($descriptionHtml === '') {
            return '';
        }

        return trim(strip_tags($descriptionHtml));
    }

    private function cmsValue(array $content, string $key): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) ? $value : '';
    }

    private function coalesce(string $value, string $fallback): string
    {
        return $value !== '' ? $value : $fallback;
    }

    private function toSlug(string $value): string
    {
        $lower = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $lower);

        return trim((string)$slug, '-');
    }
}
