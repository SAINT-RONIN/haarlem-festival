<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzArtistDetailConstants;
use App\Enums\EventTypeId;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Models\JazzArtistDetailEvent;
use App\Repositories\Interfaces\IEventRepository;

class JazzArtistDetailService
{
    /**
     * @var array<string, array{expiresAt:int, data:array<string, mixed>}>
     */
    private static array $pageCache = [];

    public function __construct(
        private readonly CmsService $cmsService,
        private readonly ScheduleService $scheduleService,
        private readonly IEventRepository $eventRepository,
    ) {
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    public function getArtistPageDataBySlug(string $slug): array
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $cached = $this->getCachedPageData($normalizedSlug);
        if ($cached !== null) {
            return $cached;
        }

        $event = $this->findJazzEventBySlug($normalizedSlug);
        $eventId = $event->eventId;

        $payload = [
            'event' => $this->buildEventPayload($event),
            'cms' => $this->cmsService->getSectionContent(
                JazzArtistDetailConstants::DETAIL_PAGE_SLUG,
                JazzArtistDetailConstants::eventSectionKey($eventId),
            ),
            'performances' => $this->buildPerformances($eventId),
        ];

        $this->setCachedPageData($normalizedSlug, $payload);

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function setCachedPageData(string $slug, array $payload): void
    {
        self::$pageCache[$slug] = [
            'expiresAt' => time() + JazzArtistDetailConstants::PAGE_CACHE_TTL_SECONDS,
            'data' => $payload,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getCachedPageData(string $slug): ?array
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
        return is_array($data) ? $data : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEventPayload(JazzArtistDetailEvent $event): array
    {
        return [
            'id' => $event->eventId,
            'title' => $event->title,
            'shortDescription' => $event->shortDescription,
            'longDescriptionHtml' => $event->longDescriptionHtml,
            'slug' => $event->slug,
        ];
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    private function normalizeSlug(string $slug): string
    {
        $normalizedSlug = trim(strtolower(rawurldecode($slug)));
        if ($normalizedSlug === '' || str_contains($normalizedSlug, '/')) {
            throw new JazzArtistDetailNotFoundException($slug);
        }

        return trim($normalizedSlug, '-');
    }

    /**
     * @throws JazzArtistDetailNotFoundException
     */
    private function findJazzEventBySlug(string $slug): JazzArtistDetailEvent
    {
        $event = $this->eventRepository->findActiveJazzBySlug($slug);
        if ($event === null) {
            throw new JazzArtistDetailNotFoundException($slug);
        }

        return $event;
    }

    private function buildPerformances(int $eventId): array
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            JazzArtistDetailConstants::SCHEDULE_PAGE_SLUG,
            EventTypeId::Jazz->value,
            JazzArtistDetailConstants::SCHEDULE_MAX_DAYS,
            $eventId,
        );
        $performances = [];

        foreach ($scheduleData['days'] ?? [] as $day) {
            foreach ($day['events'] ?? [] as $event) {
                $performances[] = $event;
            }
        }

        return $performances;
    }
}
