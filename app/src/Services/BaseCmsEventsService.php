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
 * This base class exists solely to keep CmsEventsService under a readable size.
 * It is not a service in its own right and should not be injected directly — always
 * bind ICmsEventsService to the concrete CmsEventsService subclass.
 */
abstract class BaseCmsEventsService
{
    /**
     * @param IEventRepository $eventRepository Used by resolveUniqueSlug to check slug availability.
     * @param IVenueRepository $venueRepository Used by saveRestaurantCmsItems to look up venue addresses.
     * @param ICmsRepository   $cmsRepository   Used by all CMS section and item helpers.
     */
    public function __construct(
        protected readonly IEventRepository $eventRepository,
        protected readonly IVenueRepository $venueRepository,
        protected readonly ICmsRepository $cmsRepository,
    ) {
    }

    /**
     * Returns a URL-safe slug that is unique across all events.
     *
     * If $base is empty, "event" is used as the fallback. If the chosen slug is already
     * taken, a numeric counter suffix is appended (-2, -3, …) until a free slug is found.
     *
     * @param string $base The desired slug, typically derived from the event title.
     * @return string A slug that does not yet exist in the events table.
     */
    protected function resolveUniqueSlug(string $base): string
    {
        // Empty slug means the title was blank or whitespace — use "event" as the fallback base.
        if ($base === '') {
            $base = 'event';
        }

        if (!$this->eventRepository->slugExists($base)) {
            return $base;
        }

        // Slug is taken — find the first free numbered variant (-2, -3, …).
        $counter = 2;
        while ($this->eventRepository->slugExists("{$base}-{$counter}")) {
            $counter++;
        }

        return "{$base}-{$counter}";
    }

    /**
     * Creates a CMS section for a new event on its event type's detail page, if one exists.
     *
     * When a new event is created it automatically gets a CMS section on the relevant detail
     * page (for example, storytelling-detail or jazz-detail). If no CMS config exists for this
     * event type, the method exits silently — not all event types have a CMS detail page.
     *
     * @param int $eventTypeId The event type that determines which detail page to use.
     * @param int $eventId     The newly created event whose section should be registered.
     */
    protected function autoCreateCmsSection(int $eventTypeId, int $eventId): void
    {
        $config = EventDetailCmsHelper::forEventType($eventTypeId);
        if ($config === null) {
            // Why: not every event type has a CMS detail page (e.g. restaurant-only events).
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
     * Three independent reasons can each produce a null return:
     *   1. No CMS config exists for this event type (not all types have a detail page).
     *   2. The detail page does not exist in the database.
     *   3. No CMS section has been created for this specific event yet.
     *
     * @param int $eventTypeId The event type used to locate the detail page config.
     * @param int $eventId     The event whose CMS section ID is needed.
     * @return int|null The section ID, or null when the section cannot be found.
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
     * Writes or updates restaurant-specific CMS items (stars, cuisine, address, short description).
     *
     * Restaurant metadata is stored in the CMS item system rather than on the event row itself,
     * so this method must run after the event row is saved (the section references the event ID).
     * The venue address is looked up from the linked venue record so it stays in sync with the
     * venue table rather than being typed manually. Null values are skipped so a field that was
     * not submitted does not overwrite a previously saved value.
     *
     * @param int             $eventTypeId The event type, used to find the correct CMS detail page.
     * @param int             $eventId     The event whose restaurant metadata should be saved.
     * @param EventUpsertData $data        The form data containing the restaurant fields.
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
            // Skip nulls so an unset field does not overwrite a previously saved value.
            if ($value !== null) {
                $this->cmsRepository->upsertCmsTextItem($sectionId, $key, $value);
            }
        }
    }

    /**
     * Reads back the restaurant-specific CMS items saved by saveRestaurantCmsItems().
     *
     * Maps the stored CMS item keys (e.g. "cuisine_type") back to the field names used in
     * the returned array (e.g. "cuisine") so the caller does not need to know how the CMS
     * system stores them internally. Returns nulls for any key that has not been saved yet.
     *
     * @param int $eventTypeId The event type, used to find the correct CMS detail page.
     * @param int $eventId     The event whose restaurant metadata should be loaded.
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

        // Maps CMS item keys (as stored in DB) to the field names returned by this method.
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

    /**
     * Resolves the URL of the CMS detail-page editor for a given event type.
     *
     * This URL is displayed in the event editor as a shortcut so admins can jump directly
     * to the CMS page editor for that event type's detail page. Returns null when no CMS
     * config exists for this type or when the detail page does not yet exist in the database.
     *
     * @param int $eventTypeId The event type whose detail-page editor URL should be resolved.
     * @return string|null The editor URL (e.g. "/cms/pages/12/jazz-detail/edit"), or null.
     */
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
