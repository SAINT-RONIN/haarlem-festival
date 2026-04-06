<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\SharedSectionKeys;
use App\Constants\StorytellingDetailConstants;
use App\Exceptions\StorytellingEventNotFoundException;
use App\Helpers\SlugHelper;
use App\DTOs\Domain\Filters\EventSessionFilter;
use App\Models\EventSessionLabel;
use App\DTOs\Domain\Events\StorytellingDetailEvent;
use App\DTOs\Domain\Pages\StorytellingDetailPageData;
use App\DTOs\Cms\StorytellingEventCmsData;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IStorytellingContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IStorytellingDetailService;

/**
 * Assembles the detail-page payload for a single Storytelling event.
 *
 * Combines event data, CMS overrides, featured-image resolution,
 * session labels, and about-body fallback logic from five repositories
 * into a single StorytellingDetailPageData object.
 */
class StorytellingDetailService extends BaseContentService implements IStorytellingDetailService
{
    public function __construct(
        private readonly IStorytellingContentRepository $storyContentRepo,
        private readonly IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
        IGlobalContentRepository $globalContentRepo,
    ) {
        parent::__construct($globalContentRepo);
    }

    /**
     * Assembles the full domain payload for a storytelling event detail page.
     *
     * Centralises all repository calls and resolution logic so the controller
     * stays thin and the mapper receives a ready-to-use model.
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
        return $this->storyContentRepo->findEventCmsData(
            StorytellingDetailConstants::DETAIL_PAGE_SLUG,
            SharedSectionKeys::eventSectionKey($eventId),
        );
    }

    private function buildPageData(StorytellingDetailEvent $event, StorytellingEventCmsData $cms): StorytellingDetailPageData
    {
        return new StorytellingDetailPageData(
            event: $event,
            cms: $cms,
            featuredImagePath: $this->fetchFeaturedImagePath($event),
            labels: $this->fetchEventLabels($event->eventId),
            aboutBody: $this->resolveAboutBody($cms, $event),
            globalUiContent: $this->loadGlobalUi(),
            scheduleCtaButtonText: $cms->scheduleCtaButtonText ?? '',
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
     * Converts the event's featured image asset ID into a file path
     * that the mapper can use for rendering.
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
     * Returns the best available about-section body text.
     *
     * Fallback chain: CMS aboutBody -> longDescriptionHtml -> shortDescription.
     * Content editors don't always fill the CMS field, so the service provides
     * a sensible default before the mapper receives the data.
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
     *
     * Labels (e.g. "English", "Beginner") live on sessions, not on the event
     * itself, so they must be fetched separately and attached to the page payload.
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
            fn (EventSessionLabel $label) => $label->labelText,
            $labelsMap[$sessionId] ?? [],
        );
    }
}
