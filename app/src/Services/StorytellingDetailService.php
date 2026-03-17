<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingDetailConstants;
use App\Enums\EventTypeId;
use App\Models\StorytellingDetailEvent;
use App\Models\StorytellingDetailPageData;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IScheduleService;

class StorytellingDetailService
{
    public function __construct(
        private readonly ICmsService $cmsService,
        private readonly IScheduleService $scheduleService,
        private readonly IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * @throws \RuntimeException if the event is not found or not a storytelling event
     */
    public function getDetailPageData(int $eventId): StorytellingDetailPageData
    {
        $event = $this->findStorytellingEvent($eventId);
        $cms = $this->cmsService->getSectionContent(
            StorytellingDetailConstants::DETAIL_PAGE_SLUG,
            StorytellingDetailConstants::eventSectionKey($eventId),
        );

        return new StorytellingDetailPageData(
            event: $event,
            cms: $cms,
            featuredImagePath: $this->fetchFeaturedImagePath($event),
            labels: $this->fetchEventLabels($event->eventId),
            aboutBody: $this->resolveAboutBody($cms, $event),
            scheduleSectionData: $this->scheduleService->getScheduleData(
                StorytellingDetailConstants::SCHEDULE_PAGE_SLUG,
                EventTypeId::Storytelling->value,
                StorytellingDetailConstants::SCHEDULE_MAX_DAYS,
                $eventId,
            ),
        );
    }

    /**
     * @throws \RuntimeException if the event does not exist or is not a storytelling event
     */
    private function findStorytellingEvent(int $eventId): StorytellingDetailEvent
    {
        $events = $this->eventRepository->findEvents(['eventId' => $eventId]);
        $event = $events[0] ?? null;

        if (!$event || $event->eventTypeId !== EventTypeId::Storytelling->value) {
            throw new \RuntimeException("Storytelling event {$eventId} not found.");
        }

        return new StorytellingDetailEvent(
            eventId: $event->eventId,
            title: $event->title,
            shortDescription: $event->shortDescription,
            longDescriptionHtml: $event->longDescriptionHtml,
            featuredImageAssetId: $event->featuredImageAssetId,
        );
    }

    private function fetchFeaturedImagePath(StorytellingDetailEvent $event): ?string
    {
        if ($event->featuredImageAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($event->featuredImageAssetId);

        return $asset?->filePath;
    }

    private function resolveAboutBody(array $cms, StorytellingDetailEvent $event): string
    {
        if (!empty($cms['about_body'])) {
            return $cms['about_body'];
        }

        if (!empty($event->longDescriptionHtml)) {
            return $event->longDescriptionHtml;
        }

        if (!empty($event->shortDescription)) {
            return $event->shortDescription;
        }

        return '';
    }

    /**
     * Fetches label texts for the first active session of an event.
     *
     * @return string[]
     */
    private function fetchEventLabels(int $eventId): array
    {
        $sessions = $this->sessionRepository->findSessions([
            'eventId' => $eventId,
            'isActive' => true,
        ]);
        $sessionList = $sessions['sessions'] ?? [];

        if (empty($sessionList)) {
            return [];
        }

        $firstSessionId = (int)$sessionList[0]['EventSessionId'];
        $labelsMap = $this->labelRepository->findLabels([
            'sessionIds' => [$firstSessionId],
            'groupBySession' => true,
        ]);

        $labels = $labelsMap[$firstSessionId] ?? [];
        $labelTexts = [];

        foreach ($labels as $label) {
            $labelTexts[] = $label->labelText;
        }

        return $labelTexts;
    }
}
