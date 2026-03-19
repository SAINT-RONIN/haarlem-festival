<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingDetailConstants;
use App\Models\StorytellingDetailEvent;
use App\Models\StorytellingDetailPageData;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\ICmsContentRepository;
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
     * @throws \RuntimeException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): StorytellingDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $event = $this->findStorytellingEventBySlug($normalizedSlug);
        $eventId = $event->eventId;
        $cms = $this->cmsService->getSectionContent(
            StorytellingDetailConstants::DETAIL_PAGE_SLUG,
            StorytellingDetailConstants::eventSectionKey($eventId),
        );

        return new StorytellingDetailPageData(
            event: $event,
            cms: $cms,
            featuredImagePath: $this->fetchFeaturedImagePath($event),
            labels: $this->fetchEventLabels($eventId),
            aboutBody: $this->resolveAboutBody($cms, $event),
            // TODO: change 'home' to 'global' after running the database migration in docs/global-ui-migration.md
            globalUiContent: $this->cmsService->getSectionContent('home', 'global_ui'),
        );
    }

    /**
     * Normalizes the slug to lowercase with no leading/trailing dashes.
     *
     * @throws \RuntimeException if the slug is empty or contains a path separator
     */
    private function normalizeSlug(string $slug): string
    {
        $normalized = trim(strtolower(rawurldecode($slug)));
        if ($normalized === '' || str_contains($normalized, '/')) {
            throw new \RuntimeException("Invalid storytelling event slug.");
        }
        return trim($normalized, '-');
    }

    /**
     * Fetches the storytelling event by slug, throwing if not found.
     *
     * @throws \RuntimeException if no active storytelling event matches the slug
     */
    private function findStorytellingEventBySlug(string $slug): StorytellingDetailEvent
    {
        $event = $this->eventRepository->findActiveStorytellingBySlug($slug);
        if ($event === null) {
            throw new \RuntimeException("Storytelling event not found: {$slug}");
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
     * The reason for this is because labels (e.g. "English", "Beginner") live on sessions, not on the event itself, so they must be fetched separately and attached to the page payload.
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

        $firstSessionId = $sessionList[0]->eventSessionId;
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
