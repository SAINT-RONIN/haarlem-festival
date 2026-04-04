<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CmsItemType;
use App\Enums\EventTypeId;
use App\DTOs\Cms\JazzLineupManagerData;
use App\Helpers\FormatHelper;
use App\Models\CmsItem;
use App\Repositories\Interfaces\IArtistRepository;
use App\DTOs\Filters\CmsItemFilter;
use App\Models\CmsPage;
use App\DTOs\Cms\CmsPageEditData;
use App\DTOs\Filters\CmsPageFilter;
use App\Models\CmsSection;
use App\DTOs\Cms\CmsSectionEditData;
use App\DTOs\Filters\CmsSectionFilter;
use App\DTOs\Cms\CmsUpdateResult;
use App\DTOs\Filters\EventFilter;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Exceptions\CmsOperationException;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\ICmsItemEnricher;
use App\Services\Interfaces\ICmsPreviewUrlResolver;
use App\Utils\CmsContentLimits;

/**
 * Service for CMS page editing operations.
 *
 * Handles business logic for loading, validating, and saving
 * CMS page content.
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
     * Loads a CMS page together with its sections (each enriched with editable
     * items and media-asset metadata) for rendering in the CMS editor UI.
     *
     * Returns null when the page ID does not exist.
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
     * Loads all sections and items for a page, groups items by section,
     * and enriches each with media-asset data and editor-input metadata.
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
     * Builds an editable section, skipping orphaned event sections
     * (sections whose event no longer exists in the database).
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
     * Returns a section-key-to-event-title map for detail pages so the CMS
     * editor can show the event name instead of a raw "event_42" key.
     * Returns an empty array for non-detail pages.
     *
     * @return array<string, string>
     */
    private function buildEventNameMapForPage(string $pageSlug): array
    {
        if ($pageSlug === 'storytelling-detail') {
            return $this->buildEventNameMap(EventTypeId::Storytelling->value);
        }
        return [];
    }

    private function buildJazzLineupManagerData(CmsPage $page): ?JazzLineupManagerData
    {
        if ($page->slug !== 'jazz') {
            return null;
        }

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
     * Validates and persists updates for multiple CMS items in a single form submission.
     *
     * Each item is validated against its type-specific character limit before saving.
     * Returns a result object indicating how many items were updated and any validation errors.
     *
     * @param array<int|string, mixed> $items Array of item updates: [itemId => value_string]
     * @throws \App\Exceptions\CmsEditException if an item ID does not belong to the given page
     */
    /** @throws CmsOperationException When a database write fails during batch update */
    public function updatePageItems(int $pageId, array $items): CmsUpdateResult
    {
        try {
            return $this->processPageItemUpdates($pageId, $items);
        } catch (\App\Exceptions\CmsEditException $error) {
            throw $error;
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update page items.', 0, $error);
        }
    }

    /** Iterates each item, validates, and persists changes. */
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
     * @param array<int, CmsItem> $indexedItems
     * @return true|string|null true = updated, string = validation error, null = not updated (no error)
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
     * Updates a single CMS item's media asset.
     */
    /** @throws CmsOperationException When the database write fails */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool
    {
        try {
            return $this->cmsRepository->updateItemMediaAsset($itemId, $mediaAssetId);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update item image.', 0, $error);
        }
    }

    /**
     * Builds a route-aware preview URL for CMS page edit screens.
     *
     * @param CmsSectionEditData[] $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string
    {
        return $this->previewUrlResolver->resolve($page, $sections);
    }

    /**
     * Validates a single item value.
     *
     * @return string|null Error message or null if valid
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

    private function buildCharLimitError(string $itemKey, int $maxChars): string
    {
        $label = FormatHelper::formatFieldLabel($itemKey);
        return "{$label} exceeds maximum of {$maxChars} characters";
    }

    /**
     * Prepares data array for repository update.
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
     * Moves the hero_image item to the front so it appears first in the CMS editor.
     *
     * @param CmsItem[] $items
     * @return CmsItem[]
     */
    private function sortHeroImageFirst(array $items): array
    {
        usort($items, fn(CmsItem $a, CmsItem $b) => ($b->itemKey === 'hero_image') <=> ($a->itemKey === 'hero_image'));
        return $items;
    }

    /**
     * Builds a map of section key → event title for storytelling events.
     * e.g., ['event_34' => 'Winnie de Poeh (4+)', ...]
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
     * Strips HTML for character counting.
     */
    private function stripHtmlForCount(string $value): string
    {
        return strip_tags(html_entity_decode($value));
    }
}
