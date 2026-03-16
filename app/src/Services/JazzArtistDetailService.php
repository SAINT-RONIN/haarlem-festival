<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Repositories\EventRepository;

class JazzArtistDetailService
{
    private const DETAIL_PAGE_SLUG = 'jazz-artist-detail';

    private CmsService $cmsService;
    private ScheduleService $scheduleService;
    private EventRepository $eventRepository;

    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->scheduleService = new ScheduleService();
        $this->eventRepository = new EventRepository();
    }

    /**
     * @throws \RuntimeException when no active jazz event exists for the slug.
     */
    public function getArtistPageDataBySlug(string $slug): array
    {
        $event = $this->findJazzEventBySlug($slug);
        $eventId = (int)$event['EventId'];
        $cms = $this->cmsService->getSectionContent(self::DETAIL_PAGE_SLUG, 'event_' . $eventId);
        $eventTitle = (string)($event['Title'] ?? '');

        return [
            'heroTitle' => (string)($event['Title'] ?? $eventTitle),
            'heroSubtitle' => $this->coalesce($this->cmsValue($cms, 'hero_subtitle'), (string)($event['ShortDescription'] ?? '')),
            'heroBackgroundImageUrl' => $this->cmsValue($cms, 'hero_background_image'),
            'originText' => $this->cmsValue($cms, 'origin_text'),
            'formedText' => $this->cmsValue($cms, 'formed_text'),
            'performancesText' => $this->cmsValue($cms, 'performances_text'),
            'heroBackButtonText' => $this->cmsValue($cms, 'hero_back_button_text'),
            'heroReserveButtonText' => $this->cmsValue($cms, 'hero_reserve_button_text'),
            'overviewHeading' => $this->coalesce($this->cmsValue($cms, 'overview_heading'), (string)($event['Title'] ?? '')),
            'overviewLead' => $this->coalesce($this->cmsValue($cms, 'overview_lead'), (string)($event['ShortDescription'] ?? '')),
            'overviewBodyPrimary' => $this->coalesce($this->cmsValue($cms, 'overview_body_primary'), $this->buildPrimaryOverviewFallback($event)),
            'overviewBodySecondary' => $this->cmsValue($cms, 'overview_body_secondary'),
            'lineupHeading' => $this->cmsValue($cms, 'lineup_heading'),
            'lineup' => $this->collectTextList($cms, 'lineup_'),
            'highlightsHeading' => $this->cmsValue($cms, 'highlights_heading'),
            'highlights' => $this->collectTextList($cms, 'highlight_'),
            'photoGalleryHeading' => $this->cmsValue($cms, 'photo_gallery_heading'),
            'photoGalleryDescription' => $this->cmsValue($cms, 'photo_gallery_description'),
            'galleryImages' => $this->collectTextList($cms, 'gallery_image_'),
            'albumsHeading' => $this->cmsValue($cms, 'albums_heading'),
            'albumsDescription' => $this->cmsValue($cms, 'albums_description'),
            'albums' => $this->buildAlbums($cms),
            'listenHeading' => $this->cmsValue($cms, 'listen_heading'),
            'listenSubheading' => $this->cmsValue($cms, 'listen_subheading'),
            'listenDescription' => $this->cmsValue($cms, 'listen_description'),
            'listenPlayButtonLabel' => $this->cmsValue($cms, 'listen_play_button_label'),
            'listenPlayExcerptText' => $this->cmsValue($cms, 'listen_play_excerpt_text'),
            'tracks' => $this->buildTracks($cms),
            'liveCtaHeading' => $this->cmsValue($cms, 'live_cta_heading'),
            'liveCtaDescription' => $this->cmsValue($cms, 'live_cta_description'),
            'liveCtaBookButtonText' => $this->cmsValue($cms, 'live_cta_book_button_text'),
            'liveCtaScheduleButtonText' => $this->cmsValue($cms, 'live_cta_schedule_button_text'),
            'performancesSectionId' => $this->cmsValue($cms, 'performances_section_id'),
            'performancesHeading' => $this->coalesce($this->cmsValue($cms, 'performances_heading'), (string)($event['Title'] ?? '')),
            'performancesDescription' => $this->cmsValue($cms, 'performances_description'),
            'performances' => $this->buildPerformances($eventId),
        ];
    }

    private function findJazzEventBySlug(string $slug): array
    {
        $normalizedSlug = trim(strtolower($slug));

        $events = $this->eventRepository->findEvents([
            'eventTypeId' => EventTypeId::Jazz->value,
            'isActive' => true,
        ]);

        foreach ($events as $event) {
            $eventTitle = (string)($event['Title'] ?? '');
            if ($this->toSlug($eventTitle) === $normalizedSlug) {
                return $event;
            }
        }

        throw new \RuntimeException("Jazz event slug {$slug} not found.");
    }

    private function buildPerformances(int $eventId): array
    {
        $scheduleData = $this->scheduleService->getScheduleData('jazz', EventTypeId::Jazz->value, 7, $eventId);
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

        for ($index = 1; $index <= 8; $index++) {
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

        for ($index = 1; $index <= 12; $index++) {
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

    private function collectTextList(array $cms, string $prefix): array
    {
        $values = [];

        for ($index = 1; $index <= 24; $index++) {
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
        if ($value !== '') {
            return $value;
        }

        return $fallback;
    }

    private function toSlug(string $value): string
    {
        $lower = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $lower);

        return trim((string)$slug, '-');
    }
}
