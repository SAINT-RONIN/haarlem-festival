<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\SharedSectionKeys;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Domain\Filters\CmsItemFilter;
use App\DTOs\Domain\Filters\CmsSectionFilter;
use App\DTOs\Domain\Restaurant\RestaurantCmsData;
use App\Helpers\EventDetailCmsHelper;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IVenueRepository;

// Shared CMS event helpers (slug resolution, restaurant metadata, detail-page sections).
// Always bind ICmsEventsService to CmsEventsService, not this class directly.
abstract class BaseCmsEventsService
{
    public function __construct(
        protected readonly IEventRepository $eventRepository,
        protected readonly IVenueRepository $venueRepository,
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

    protected function findEventCmsSectionId(int $eventTypeId, int $eventId): ?int
    {
        $config = EventDetailCmsHelper::forEventType($eventTypeId);
        if ($config === null) {
            return null;
        }

        $page = $this->cmsRepository->findPageBySlug($config->detailPageSlug);
        if ($page === null) {
            return null;
        }

        $sections = $this->cmsRepository->findSections(
            new CmsSectionFilter(
                cmsPageId: $page->cmsPageId,
                sectionKey: SharedSectionKeys::eventSectionKey($eventId),
            )
        );

        return $sections !== [] ? $sections[0]->cmsSectionId : null;
    }

    // Restaurant metadata lives in the CMS item system, not on the event row.
    // Null values are skipped to avoid overwriting previously saved data.
    protected function saveRestaurantCmsItems(int $eventTypeId, int $eventId, EventUpsertData $data): void
    {
        $sectionId = $this->findEventCmsSectionId($eventTypeId, $eventId);
        if ($sectionId === null) {
            return;
        }

        $venueAddress = null;
        if ($data->venueId !== null) {
            // Address is sourced from the linked venue so it stays consistent with the venue record.
            $venue = $this->venueRepository->findById($data->venueId);
            if ($venue !== null && $venue->addressLine !== '') {
                $venueAddress = $venue->addressLine;
            }
        }

        $items = [
            'stars'        => $data->restaurantStars !== null ? (string) $data->restaurantStars : null,
            'cuisine_type' => $data->restaurantCuisine,
            'about_text'   => $data->restaurantShortDescription,
            'address_line' => $venueAddress,
        ];

        foreach ($items as $key => $value) {
            if ($value !== null) {
                $this->cmsRepository->upsertCmsTextItem($sectionId, $key, $value);
            }
        }
    }

    protected function loadRestaurantCmsItems(int $eventTypeId, int $eventId): RestaurantCmsData
    {
        $sectionId = $this->findEventCmsSectionId($eventTypeId, $eventId);
        if ($sectionId === null) {
            return new RestaurantCmsData();
        }

        $items = $this->cmsRepository->findItems(
            new CmsItemFilter(cmsSectionId: $sectionId)
        );

        $stars             = null;
        $cuisine           = null;
        $shortDescription  = null;

        foreach ($items as $item) {
            match ($item->itemKey) {
                'stars'        => $stars            = $item->textValue,
                'cuisine_type' => $cuisine           = $item->textValue,
                'about_text'   => $shortDescription  = $item->textValue,
                default        => null,
            };
        }

        return new RestaurantCmsData($stars, $cuisine, $shortDescription);
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
