<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzPageConstants;
use App\Constants\RestaurantDetailConstants;
use App\Constants\StorytellingDetailConstants;
use App\Enums\CmsItemType;
use App\Enums\EventTypeId;
use App\DTOs\Cms\JazzLineupManagerData;
use App\Helpers\FormatHelper;
use App\Models\CmsItem;
use App\Repositories\Interfaces\IArtistRepository;
use App\DTOs\Domain\Filters\CmsItemFilter;
use App\Models\CmsPage;
use App\DTOs\Cms\CmsPageEditData;
use App\DTOs\Domain\Filters\CmsPageFilter;
use App\Models\CmsSection;
use App\DTOs\Cms\CmsSectionEditData;
use App\DTOs\Domain\Filters\CmsSectionFilter;
use App\DTOs\Cms\CmsUpdateResult;
use App\DTOs\Domain\Filters\EventFilter;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Exceptions\CmsOperationException;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\ICmsItemEnricher;
use App\Services\Interfaces\ICmsPreviewUrlResolver;
use App\Utils\CmsContentLimits;

/**
 * Loads CMS pages for editing and saves content changes made in the editor.
 *
 * When loading a page, all sections and items are fetched in two queries (not N+1) and then
 * assembled in memory. When saving, each item is validated against its type-specific character
 * limit before being written, and a result object is returned so the editor can show success
 * counts and per-item errors to the admin.
 */
class CmsEditService implements ICmsEditService
{
    public function __construct(
        private readonly ICmsRepository $cmsRepository,
        private readonly IEventRepository $eventRepository,
        private readonly IArtistRepository $artistRepository,
        private readonly ICmsItemEnricher $itemEnricher,
        private readonly ICmsPreviewUrlResolver $previewUrlResolver,
    ) {
    }

    /**
     * Loads a CMS page with all its sections and items, ready for the editor view.
     *
     * Returns null when the page id doesn't exist — the controller uses null to send a 404.
     * Sections and items are loaded in two queries (not one per section) and assembled in
     * memory to avoid N+1 database calls. The Jazz lineup manager panel is only populated
     * for the jazz page; all other pages get null and don't show the panel.
     */
    public function getPageForEditing(int $pageId): ?CmsPageEditData
    {
        $pages = $this->cmsRepository->findPages(new CmsPageFilter(cmsPageId: $pageId));
        $page = $pages[0] ?? null;
        if ($page === null) {
            return null;
        }

        $sections = $this->buildSectionsWithItems($page);

        return new CmsPageEditData(
            page: $page,
            sections: $sections,
            jazzLineupManager: $this->buildJazzLineupManagerData($page),
        );
    }

    /**
     * Fetches all sections and items for a page and groups them for the editor.
     *
     * Items are loaded once for the whole page (not per section) and grouped in memory
     * to avoid N+1 queries. Each section then receives only the items that belong to it.
     *
     * @return CmsSectionEditData[]
     */
    private function buildSectionsWithItems(CmsPage $page): array
    {
        // Load all sections and items for this page in two queries
        $sections = $this->cmsRepository->findSections(new CmsSectionFilter(cmsPageId: $page->cmsPageId));
        $items = $this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $page->cmsPageId));
        $itemsBySection = $this->groupItemsBySection($items);

        // For detail pages, map event section keys to human-readable event names
        $eventNameMap = $this->buildEventNameMapForPage($page->slug);

        return $this->assembleSections($sections, $itemsBySection, $eventNameMap);
    }

    /**
     * Loops through all sections, builds each one, and returns only those that are valid.
     *
     * Sections where buildSingleSection returns null are silently skipped — this is how
     * the editor hides sections that belong to deleted or inactive events.
     *
     * @param CmsSection[] $sections
     * @param array<int, list<CmsItem>> $itemsBySection
     * @param array<string, string> $eventNameMap
     * @return CmsSectionEditData[]
     */
    private function assembleSections(array $sections, array $itemsBySection, array $eventNameMap): array
    {
        $result = [];
        foreach ($sections as $section) {
            $built = $this->buildSingleSection($section, $itemsBySection, $eventNameMap);
            if ($built !== null) {
                $result[] = $built;
            }
        }
        return $result;
    }

    /**
     * Builds one editable section, or returns null if it should be hidden from the editor.
     *
     * A section key like "event_42" is a per-event dynamic section. When the event no longer
     * appears in the name map (deleted or inactive) the section is skipped by returning null,
     * so the editor doesn't show broken sections the admin can't do anything about.
     *
     * @param array<int, list<CmsItem>> $itemsBySection
     * @param array<string, string> $eventNameMap
     */
    private function buildSingleSection(CmsSection $section, array $itemsBySection, array $eventNameMap): ?CmsSectionEditData
    {
        // Skip event sections that reference a deleted/inactive event
        if ($eventNameMap !== [] && str_starts_with($section->sectionKey, 'event_') && !isset($eventNameMap[$section->sectionKey])) {
            return null;
        }

        $sectionItems = $this->sortHeroImageFirst($itemsBySection[$section->cmsSectionId] ?? []);
        $displayName = $eventNameMap[$section->sectionKey] ?? $section->sectionKey;

        return new CmsSectionEditData(
            sectionId: $section->cmsSectionId,
            sectionKey: $section->sectionKey,
            displayName: $displayName,
            items: $this->itemEnricher->enrichItems($sectionItems),
        );
    }

    /**
     * Builds a map of section key to event title for pages that have per-event dynamic sections.
     *
     * Only detail pages have dynamic event sections (e.g. "event_42"). Other pages use
     * static section keys and don't need this map, so an empty array is returned for them.
     *
     * @return array<string, string>
     */
    private function buildEventNameMapForPage(string $pageSlug): array
    {
        if ($pageSlug === StorytellingDetailConstants::DETAIL_PAGE_SLUG) {
            return $this->buildEventNameMap(EventTypeId::Storytelling->value);
        }
        if ($pageSlug === RestaurantDetailConstants::PAGE_SLUG) {
            return $this->buildEventNameMap(EventTypeId::Restaurant->value);
        }
        // More detail-page slugs can be added here when their pages support per-event sections.
        return [];
    }

    /**
     * Builds the Jazz lineup manager panel data, or returns null for non-jazz pages.
     *
     * The manager panel lets admins pick which artists appear in the Jazz overview grid.
     * "Available" artists are those who are active but not yet shown in the grid.
     * Non-jazz pages get null so the view knows not to render the panel at all.
     */
    private function buildJazzLineupManagerData(CmsPage $page): ?JazzLineupManagerData
    {
        if ($page->slug !== JazzPageConstants::PAGE_SLUG) {
            return null;
        }

        // An artist is available to add if they are active but not yet shown on the jazz overview grid.
        $availableArtists = array_values(array_filter(
            $this->artistRepository->findAll(),
            static fn(\App\Models\Artist $artist): bool => $artist->isActive && !$artist->showOnJazzOverview,
        ));

        return new JazzLineupManagerData(
            visibleArtists: $this->eventRepository->findJazzOverviewArtists(),
            availableArtists: $availableArtists,
        );
    }

    /**
     * Validates and saves a batch of CMS item updates submitted from the editor form.
     *
     * Returns a CmsUpdateResult (not void) so the editor can show how many items changed
     * and display any per-item validation errors without a full page reload.
     * CmsEditException is re-thrown unwrapped because it already has a user-facing message
     * and should not be wrapped in a generic "Failed to update" message.
     *
     * @param array<int|string, mixed> $items Array of item updates: [itemId => value_string]
     * @throws \App\Exceptions\CmsEditException When an item id does not belong to this page
     * @throws CmsOperationException When a database write fails
     */
    public function updatePageItems(int $pageId, array $items): CmsUpdateResult
    {
        try {
            return $this->processPageItemUpdates($pageId, $items);
        } catch (\App\Exceptions\CmsEditException $error) {
            // CmsEditException already has a useful message; let it bubble up without wrapping.
            throw $error;
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update page items.', 0, $error);
        }
    }

    /**
     * Loops through each item, validates it, and saves it if valid.
     *
     * processSingleItem returns true on success, a string error message on validation failure,
     * or null when the repository reported no change (not an error). All errors are collected
     * so the editor can display all problems at once.
     */
    private function processPageItemUpdates(int $pageId, array $items): CmsUpdateResult
    {
        $errors = [];
        $updatedCount = 0;
        // Pre-load all items for the page so each update can be validated against the correct type
        $pageItemsById = $this->indexPageItemsById($pageId);

        foreach ($items as $itemId => $rawValue) {
            $result = $this->processSingleItem((int)$itemId, (string)$rawValue, $pageItemsById);
            if ($result === true) {
                $updatedCount++;
            } elseif (is_string($result)) {
                $errors[] = $result;
            }
        }

        return new CmsUpdateResult(success: $errors === [], updatedCount: $updatedCount, errors: $errors);
    }

    /**
     * Validates and saves one CMS item update.
     *
     * Returns true when the item was saved, a string error message when validation failed,
     * or null when the repository returned false without an error (no change made).
     * Throws CmsEditException when the item id doesn't exist on this page — that is a
     * programming error, not a user-facing validation failure.
     *
     * @param array<int, CmsItem> $indexedItems
     * @return true|string|null
     */
    private function processSingleItem(int $itemId, string $rawValue, array $indexedItems): true|string|null
    {
        $item = $indexedItems[$itemId] ?? null;
        if ($item === null) {
            throw new \App\Exceptions\CmsEditException("Item ID {$itemId} not found");
        }

        $type = $item->itemType;
        $validationError = $this->validateItemValue($rawValue, $type, $item->itemKey);
        if ($validationError !== null) {
            return $validationError;
        }

        return $this->cmsRepository->updateItem($itemId, $this->prepareUpdateData($rawValue, $type)) ? true : null;
    }

    /**
     * Loads all items for a page and returns them indexed by their id.
     *
     * The index is keyed by cmsItemId so each update in the loop can find its target
     * item in O(1) instead of scanning the whole array on every iteration.
     *
     * @return array<int, CmsItem>
     */
    private function indexPageItemsById(int $pageId): array
    {
        $pageItems = $this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId));
        $indexed = [];
        foreach ($pageItems as $pageItem) {
            $indexed[$pageItem->cmsItemId] = $pageItem;
        }
        return $indexed;
    }

    /**
     * Replaces the media asset linked to a CMS item.
     *
     * Used when an admin uploads a new image for a specific slot in the page editor.
     * Returns true when the link was updated, false when nothing changed.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool
    {
        try {
            return $this->cmsRepository->updateItemMediaAsset($itemId, $mediaAssetId);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update item image.', 0, $error);
        }
    }

    /**
     * Returns the URL where the admin can preview the current page in the public site.
     *
     * The URL is page-type-specific — the resolver knows the routing rules for each
     * page type so this service doesn't have to. Used for the "Preview page" button
     * in the CMS editor.
     *
     * @param CmsSectionEditData[] $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string
    {
        return $this->previewUrlResolver->resolve($page, $sections);
    }

    /**
     * Checks that a value does not exceed the character limit for its item type.
     *
     * HTML tags are stripped before counting so a "<b>hello</b>" counts as 5 characters,
     * not 18 — this matches what the user actually sees in the rendered page.
     *
     * @return string|null Error message, or null if the value is within the limit
     */
    private function validateItemValue(string $value, CmsItemType $type, string $itemKey): ?string
    {
        $maxChars = CmsContentLimits::getCharLimitForType($type->value);
        $plainText = $this->stripHtmlForCount($value);

        if (strlen($plainText) > $maxChars) {
            return $this->buildCharLimitError($itemKey, $maxChars);
        }

        return null;
    }

    /**
     * Builds a human-readable error message for a character limit violation.
     *
     * The item key (e.g. "hero_title") is formatted to a label (e.g. "Hero Title")
     * so the message shown to the admin makes sense without knowing internal key names.
     */
    private function buildCharLimitError(string $itemKey, int $maxChars): string
    {
        $label = FormatHelper::formatFieldLabel($itemKey);
        return "{$label} exceeds maximum of {$maxChars} characters";
    }

    /**
     * Converts a raw editor value into the column map the repository expects.
     *
     * HTML items keep their markup and clear the text column.
     * TEXT items have their HTML stripped and entities decoded before storing,
     * because text slots are rendered as-is (no HTML parser in the view).
     * Both cases explicitly null out the column they don't use to avoid stale data.
     *
     * @return array{HtmlValue: ?string, TextValue: ?string}
     */
    private function prepareUpdateData(string $value, CmsItemType $type): array
    {
        if ($type === CmsItemType::Html) {
            return ['HtmlValue' => $value, 'TextValue' => null];
        }

        // TEXT items strip all HTML and decode entities so the stored value is always plain text
        if ($type === CmsItemType::Text) {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plain = trim(strip_tags($decoded));
            return ['TextValue' => $plain, 'HtmlValue' => null];
        }

        return ['TextValue' => $value, 'HtmlValue' => null];
    }

    /**
     * Groups a flat list of items by their section id.
     *
     * Items are fetched in a single query for the whole page. This method splits them
     * into per-section buckets in memory so the section builder can pick up its items
     * without making additional database calls.
     *
     * @param CmsItem[] $items
     * @return array<int, list<CmsItem>>
     */
    private function groupItemsBySection(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->cmsSectionId][] = $item;
        }

        return $grouped;
    }

    /**
     * Moves the hero image item to the top of the list in the editor.
     *
     * The hero image is always shown first regardless of the database order, so it
     * is easy to find and replace without scrolling through all the text items.
     *
     * @param CmsItem[] $items
     * @return CmsItem[]
     */
    private function sortHeroImageFirst(array $items): array
    {
        // The <=> comparison returns 1 when b is hero_image, pushing it to the front.
        // When neither item is hero_image the order stays unchanged.
        usort($items, fn(CmsItem $a, CmsItem $b) => ($b->itemKey === 'hero_image') <=> ($a->itemKey === 'hero_image'));
        return $items;
    }

    /**
     * Builds a map from section key to event title for a given event type.
     *
     * Each entry maps a key like "event_42" to the event's human-readable title
     * (e.g. "Winnie de Poeh") so the editor shows the title instead of the raw key.
     * Only active events are included so deleted events don't appear in the map.
     *
     * @return array<string, string>
     */
    private function buildEventNameMap(int $eventTypeId): array
    {
        $events = $this->eventRepository->findEvents(new EventFilter(eventTypeId: $eventTypeId, isActive: true));
        $map = [];
        foreach ($events as $event) {
            $map['event_' . $event->eventId] = $event->title;
        }
        return $map;
    }

    /**
     * Removes HTML tags from a value and returns the plain text used for character counting.
     *
     * HTML entities are decoded first so "&amp;" counts as 1 character, not 5 — this
     * matches what the user actually reads on the page.
     */
    private function stripHtmlForCount(string $value): string
    {
        return strip_tags(html_entity_decode($value));
    }
}


