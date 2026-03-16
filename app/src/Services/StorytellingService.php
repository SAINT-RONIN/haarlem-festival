<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Repositories\EventRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IStorytellingService;

class StorytellingService implements IStorytellingService
{
    private const PAGE_SLUG = 'storytelling';
    private const DETAIL_PAGE_SLUG = 'storytelling-detail';
    private const SCHEDULE_MAX_DAYS = 7;

    private CmsService $cmsService;
    private IScheduleService $scheduleService;
    private EventRepository $eventRepository;
    private EventSessionRepository $sessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private MediaAssetRepository $mediaAssetRepository;

    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->scheduleService = new ScheduleService();
        $this->eventRepository = new EventRepository();
        $this->sessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
    }

    public function getStorytellingPageData(): array
    {
        $globalUiContent = $this->cmsService->getGlobalUiContent();

        return [
            'heroContent' => $this->cmsService->getHeroSectionContent(self::PAGE_SLUG),
            'currentPage' => self::PAGE_SLUG,
            'globalUiContent' => $globalUiContent['content'],
            'isLoggedIn' => $globalUiContent['isLoggedIn'],
            'gradientContent' => $this->cmsService->getSectionContent(self::PAGE_SLUG, 'gradient_section'),
            'introContent' => $this->cmsService->getSectionContent(self::PAGE_SLUG, 'intro_split_section'),
            'masonryContent' => $this->cmsService->getSectionContent(self::PAGE_SLUG, 'masonry_section'),
            'scheduleData' => $this->scheduleService->getScheduleData(self::PAGE_SLUG, EventTypeId::Storytelling->value, self::SCHEDULE_MAX_DAYS),
        ];
    }

    /**
     * @throws \RuntimeException if the event is not found or not a storytelling event
     */
    public function getStorytellingDetailPageData(int $eventId): array
    {
        $event = $this->findStorytellingEvent($eventId);
        $cms = $this->cmsService->getSectionContent(self::DETAIL_PAGE_SLUG, 'event_' . $eventId);
        $globalUiContent = $this->cmsService->getGlobalUiContent();

        $eventTitle = (string)($event['Title'] ?? '');
        $eventSubtitle = (string)($event['ShortDescription'] ?? '');

        return [
            'globalUiContent' => $globalUiContent['content'],
            'isLoggedIn' => $globalUiContent['isLoggedIn'],
            'eventTitle' => $eventTitle,
            'eventSubtitle' => $eventSubtitle,
            'featuredImagePath' => $this->fetchFeaturedImagePath($event),
            'labels' => $this->fetchEventLabels($eventId),
            'aboutBodyHtml' => $this->resolveAboutBody($cms, $event),
            'cms' => $cms,
            'scheduleData' => $this->scheduleService->getScheduleData(self::PAGE_SLUG, EventTypeId::Storytelling->value, self::SCHEDULE_MAX_DAYS, $eventId),
        ];
    }

    /**
     * Returns the "about" body text for a detail page.
     * Prefers CMS content, falls back to the event's own description.
     */
    private function resolveAboutBody(array $cms, array $event): string
    {
        if (!empty($cms['about_body'])) {
            return $cms['about_body'];
        }

        if (!empty($event['LongDescriptionHtml'])) {
            return $event['LongDescriptionHtml'];
        }

        if (!empty($event['ShortDescription'])) {
            return $event['ShortDescription'];
        }

        return '';
    }

    /**
     * @throws \RuntimeException if the event does not exist or is not a storytelling event
     */
    private function findStorytellingEvent(int $eventId): array
    {
        $events = $this->eventRepository->findEvents(['eventId' => $eventId]);
        $event = $events[0] ?? null;

        if (!$event || (int)$event['EventTypeId'] !== EventTypeId::Storytelling->value) {
            throw new \RuntimeException("Storytelling event {$eventId} not found.");
        }

        return $event;
    }

    private function fetchFeaturedImagePath(array $event): ?string
    {
        if (empty($event['FeaturedImageAssetId'])) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById((int)$event['FeaturedImageAssetId']);

        if ($asset === null) {
            return null;
        }

        return $asset->filePath;
    }

    /**
     * Fetches label texts (e.g. "English", "Beginner") for the first active session of an event.
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

        // Use the first session to get representative labels for this event
        $firstSessionId = (int)$sessionList[0]['EventSessionId'];

        $labelsMap = $this->labelRepository->findLabels([
            'sessionIds' => [$firstSessionId],
            'groupBySession' => true,
        ]);

        $labels = $labelsMap[$firstSessionId] ?? [];

        // Extract just the label text from each label object
        $labelTexts = [];
        foreach ($labels as $label) {
            $labelTexts[] = $label->labelText;
        }

        return $labelTexts;
    }
}
