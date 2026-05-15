<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\SharedSectionKeys;
use App\Helpers\EventDetailCmsHelper;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;

// Shared CMS event helpers (slug resolution, detail-page sections).
// Always bind ICmsEventsService to CmsEventsService, not this class directly.
abstract class BaseCmsEventsService
{
    public function __construct(
        protected readonly IEventRepository $eventRepository,
        protected readonly ICmsRepository $cmsRepository,
    ) {}

    protected function resolveUniqueSlug(string $base): string
    {
        if ($base === '') {
            $base = 'event';
        }

        if (!$this->eventRepository->slugExists($base)) {
            return $base;
        }

        $counter = 2;
        $maxAttempts = 1000;
        while ($this->eventRepository->slugExists("{$base}-{$counter}")) {
            $counter++;
            if ($counter > $maxAttempts) {
                throw new \RuntimeException("Could not generate a unique slug after {$maxAttempts} attempts.");
            }
        }

        return "{$base}-{$counter}";
    }

    // No-op when no detail page is configured for this event type.
    protected function autoCreateCmsSection(int $eventTypeId, int $eventId): void
    {
        $config = EventDetailCmsHelper::forEventType($eventTypeId);
        if ($config === null) {
            return;
        }

        $page = $this->cmsRepository->findPageBySlug($config->detailPageSlug);
        if ($page === null) {
            return;
        }

        $this->cmsRepository->insertSection($page->cmsPageId, SharedSectionKeys::eventSectionKey($eventId));
    }

    protected function resolveCmsDetailEditUrl(int $eventTypeId): ?string
    {
        $config = EventDetailCmsHelper::forEventType($eventTypeId);
        if ($config === null) {
            return null;
        }

        $page = $this->cmsRepository->findPageBySlug($config->detailPageSlug);
        if ($page === null) {
            return null;
        }

        return "/cms/pages/{$page->cmsPageId}/{$page->slug}/edit";
    }
}
