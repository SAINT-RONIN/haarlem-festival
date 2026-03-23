<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\StorytellingDetailConstants;
use App\Exceptions\StorytellingEventNotFoundException;
use App\Helpers\SlugHelper;
use App\Models\EventSessionFilter;
use App\Models\EventSessionLabel;
use App\Models\GlobalUiContent;
use App\Models\StorytellingDetailEvent;
use App\Models\StorytellingDetailPageData;
use App\Models\StorytellingEventCmsData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IStorytellingDetailService;

class StorytellingDetailService implements IStorytellingDetailService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
        private readonly IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * Assembles the full domain payload for a storytelling event detail page.
     * The reason for this is because all repository calls and resolution logic must stay in the service so the controller stays thin and the mapper receives a ready-to-use model.
     *
     * @throws StorytellingEventNotFoundException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): StorytellingDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $event = $this->findStorytellingEventBySlug($normalizedSlug);
        $cms = $this->fetchCmsContent($event->eventId);

        return $this->buildPageData($event, $cms);
    }

    private function fetchCmsContent(int $eventId): StorytellingEventCmsData
    {
        $raw = $this->cmsService->getSectionContent(
            StorytellingDetailConstants::DETAIL_PAGE_SLUG,
            StorytellingDetailConstants::eventSectionKey($eventId),
        );
        return StorytellingEventCmsData::fromRawArray($raw);
    }

    private function buildPageData(StorytellingDetailEvent $event, StorytellingEventCmsData $cms): StorytellingDetailPageData
    {
        return new StorytellingDetailPageData(
            event: $event,
            cms: $cms,
            featuredImagePath: $this->fetchFeaturedImagePath($event),
            labels: $this->fetchEventLabels($event->eventId),
            aboutBody: $this->resolveAboutBody($cms, $event),
            globalUiContent: $this->fetchGlobalUiContent(),
            scheduleCtaButtonText: $cms->scheduleCtaButtonText ?? '',
        );
    }

    private function fetchGlobalUiContent(): GlobalUiContent
    {
        return GlobalUiContent::fromRawArray(
            $this->cmsService->getSectionContent(GlobalUiConstants::PAGE_SLUG, GlobalUiConstants::SECTION_KEY),
        );
    }

    /**
     * Normalizes the slug to lowercase with no leading/trailing dashes.
     *
     * @throws StorytellingEventNotFoundException if the slug is empty or contains a path separator
     */
    private function normalizeSlug(string $slug): string
    {
        return SlugHelper::normalize($slug) ?? throw new StorytellingEventNotFoundException($slug);
    }

    /**
     * Fetches the storytelling event by slug, throwing if not found.
     *
     * @throws StorytellingEventNotFoundException if no active storytelling event matches the slug
     */
    private function findStorytellingEventBySlug(string $slug): StorytellingDetailEvent
    {
        $event = $this->eventRepository->findActiveStorytellingBySlug($slug);
        if ($event === null) {
            throw new StorytellingEventNotFoundException($slug);
        }
        return $event;
    }

    /**
     * Resolves the file path for the event's featured image asset.
     * The reason for this is because the image asset ID stored on the event must be converted to a path before the mapper can use it.
     */
    private function fetchFeaturedImagePath(StorytellingDetailEvent $event): ?string
    {
        if ($event->featuredImageAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($event->featuredImageAssetId);

        return $asset?->filePath;
    }

    /**
     * Returns the best available about-section body text, falling back from CMS to event descriptions.
     * The reason for this is because content editors may not always fill the CMS field, so the service provides a sensible fallback chain before the mapper receives the data.
     */
    private function resolveAboutBody(StorytellingEventCmsData $cms, StorytellingDetailEvent $event): string
    {
        $aboutBody = $cms->aboutBody;
        return ($aboutBody !== null && $aboutBody !== '')
            ? $aboutBody
            : ($event->longDescriptionHtml ?: $event->shortDescription ?: '');
    }

    /**
     * Fetches label texts for the first active session of an event.
     * The reason for this is because labels (e.g. "English", "Beginner") live on sessions, not on the event itself, so they must be fetched separately and attached to the page payload.
     *
     * @return string[]
     */
    private function fetchEventLabels(int $eventId): array
    {
        $sessions = $this->sessionRepository->findSessions(
            new EventSessionFilter(eventId: $eventId, isActive: true),
        );
        $sessionList = $sessions->sessions;
        if (empty($sessionList)) {
            return [];
        }
        return $this->fetchLabelTextsForSession($sessionList[0]->eventSessionId);
    }

    /**
     * @return string[]
     */
    private function fetchLabelTextsForSession(int $sessionId): array
    {
        $labelsMap = $this->labelRepository->findLabelsBySessionIds([$sessionId]);
        return array_map(
            fn(EventSessionLabel $label) => $label->labelText,
            $labelsMap[$sessionId] ?? [],
        );
    }
}
