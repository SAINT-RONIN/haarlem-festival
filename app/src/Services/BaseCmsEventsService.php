<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\SharedSectionKeys;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Filters\CmsItemFilter;
use App\DTOs\Filters\CmsSectionFilter;
use App\Helpers\EventDetailCmsHelper;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IVenueRepository;

/**
 * Shared infrastructure for CMS event services.
 *
 * Holds the three repositories and the protected helpers that deal with the
 * restaurant CMS integration (dynamic per-event CMS sections, restaurant metadata
 * items, and the CMS detail-page editor URL). CmsEventsService extends this class
 * and calls these helpers from its public methods.
 *
 * Not a service in its own right — always bind ICmsEventsService to CmsEventsService.
 */
abstract class BaseCmsEventsService
{
    public function __construct(
        protected readonly IEventRepository $eventRepository,
        protected readonly IVenueRepository $venueRepository,
        protected readonly ICmsRepository $cmsRepository,
    ) {
    }

    /**
     * Returns a URL-safe slug that is unique across all events.
     * If the base is taken, appends -2, -3, … until a free slug is found.
     */
    protected function resolveUniqueSlug(string $base): string
    {
        if ($base === '') {
            $base = 'event';
        }

        if (!$this->eventRepository->slugExists($base)) {
            return $base;
        }

        $counter = 2;
        while ($this->eventRepository->slugExists("{$base}-{$counter}")) {
            $counter++;
        }

        return "{$base}-{$counter}";
    }

    /**
     * Creates a CMS section for a new event on its event type's detail page, if one exists.
     * Not every event type has a CMS detail page — the method exits silently when none is configured.
     */
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

    /**
     * Returns the CMS section ID for a per-event dynamic section, or null when not found.
     *
     * Three independent reasons can each produce null:
     * 1. No CMS config exists for this event type.
     * 2. The detail page does not exist in the database.
     * 3. No CMS section has been created for this specific event yet.
     */
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
                cmsPageId:  $page->cmsPageId,
                sectionKey: SharedSectionKeys::eventSectionKey($eventId),
            )
        );

        return $sections !== [] ? $sections[0]->cmsSectionId : null;
    }

    /**
     * Writes restaurant-specific CMS items (stars, cuisine, address, short description).
     *
     * Restaurant metadata lives in the CMS item system, not on the event row itself,
     * so this must run after the event row is saved. Null values are skipped to avoid
     * overwriting previously saved data when a field was not submitted.
     */
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

    /**
     * Reads back the restaurant CMS items saved by saveRestaurantCmsItems().
     * Maps DB item keys (e.g. "cuisine_type") to the field names used in the returned array.
     *
     * @return array{stars: ?string, cuisine: ?string, shortDescription: ?string}
     */
    protected function loadRestaurantCmsItems(int $eventTypeId, int $eventId): array
    {
        $result = ['stars' => null, 'cuisine' => null, 'shortDescription' => null];

        $sectionId = $this->findEventCmsSectionId($eventTypeId, $eventId);
        if ($sectionId === null) {
            return $result;
        }

        $items = $this->cmsRepository->findItems(
            new CmsItemFilter(cmsSectionId: $sectionId)
        );

        $keyMap = [
            'stars'        => 'stars',
            'cuisine_type' => 'cuisine',
            'about_text'   => 'shortDescription',
        ];

        foreach ($items as $item) {
            if (isset($keyMap[$item->itemKey])) {
                $result[$keyMap[$item->itemKey]] = $item->textValue;
            }
        }

        return $result;
    }

    /** @return string|null The editor URL (e.g. "/cms/pages/12/jazz-detail/edit"), or null. */
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
