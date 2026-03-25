<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CmsMessages;
use App\Enums\EventTypeId;
use App\Helpers\FormatHelper;
use App\Models\CmsItem;
use App\DTOs\Cms\CmsItemEditData;
use App\DTOs\Filters\CmsItemFilter;
use App\DTOs\Cms\CmsMediaAssetData;
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
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Exceptions\CmsOperationException;
use App\Services\Interfaces\ICmsEditService;
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
        private readonly IMediaAssetRepository $mediaAssetRepository,
        private readonly IEventRepository $eventRepository,
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

        return new CmsPageEditData(page: $page, sections: $sections);
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
            items: $this->enrichItemsWithMetadata($sectionItems),
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
        if ($pageSlug === 'jazz-artist-detail') {
            return $this->buildEventNameMap(EventTypeId::Jazz->value);
        }
        return [];
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

        $type = $item->itemType->value;
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
     * For detail pages (storytelling-detail, jazz-artist-detail, restaurant-detail)
     * it extracts the first event name/ID from the sections to build a slug-based URL.
     * For all other pages it returns /{pageSlug} (or "/" for "home").
     *
     * @param CmsSectionEditData[] $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string
    {
        if ($page->slug === 'home') {
            return '/';
        }

        $detailUrl = $this->resolveDetailPageUrl($page->slug, $sections);
        return $detailUrl ?? '/' . $page->slug;
    }

    /**
     * @param CmsSectionEditData[] $sections
     */
    private function resolveDetailPageUrl(string $slug, array $sections): ?string
    {
        if ($slug === 'storytelling-detail') {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null ? '/storytelling/' . $this->toSlug($eventName) : '/storytelling';
        }

        if ($slug === 'jazz-artist-detail') {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null ? '/jazz/' . $this->toSlug($eventName) : '/jazz';
        }

        if ($slug === 'restaurant-detail') {
            $eventId = $this->extractFirstEventId($sections);
            return $eventId !== null ? '/restaurant/' . $eventId : '/restaurant';
        }

        return null;
    }

    /**
     * Validates a single item value.
     *
     * @return string|null Error message or null if valid
     */
    private function validateItemValue(string $value, string $type, string $itemKey): ?string
    {
        $maxChars = CmsContentLimits::getCharLimitForType($type);
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
    private function prepareUpdateData(string $value, string $type): array
    {
        $normalizedType = strtoupper($type);

        if ($normalizedType === 'HTML') {
            return ['HtmlValue' => $value, 'TextValue' => null];
        }

        // TEXT items strip all HTML and decode entities so the stored value is always plain text
        if ($normalizedType === 'TEXT') {
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
     * @param CmsItem[] $items
     * @return CmsItemEditData[]
     */
    private function enrichItemsWithMetadata(array $items): array
    {
        $mediaAssets = $this->loadMediaAssetsForItems($items);
        $enriched = [];

        foreach ($items as $item) {
            $enriched[] = $this->enrichSingleItem($item, $mediaAssets);
        }

        return $enriched;
    }

    /**
     * @param array<int, \App\Models\MediaAsset> $mediaAssets
     */
    private function enrichSingleItem(CmsItem $item, array $mediaAssets): CmsItemEditData
    {
        $mediaAsset = $this->resolveMediaAsset($item, $mediaAssets);
        $resolvedFilePath = $this->resolveFilePath($item, $mediaAsset);
        $inputType = $this->resolveInputType($item);

        return $this->buildEnrichedItem($item, $mediaAsset, $resolvedFilePath, $inputType);
    }

    /**
     * Batch-loads media assets for all items that reference one.
     *
     * @param CmsItem[] $items
     * @return array<int, \App\Models\MediaAsset>
     */
    private function loadMediaAssetsForItems(array $items): array
    {
        $mediaAssetIds = array_values(array_filter(
            array_map(fn(CmsItem $item) => $item->mediaAssetId, $items)
        ));

        return $mediaAssetIds !== [] ? $this->mediaAssetRepository->findByIds($mediaAssetIds) : [];
    }

    private function resolveMediaAsset(CmsItem $item, array $mediaAssets): ?\App\Models\MediaAsset
    {
        return $item->mediaAssetId !== null ? ($mediaAssets[$item->mediaAssetId] ?? null) : null;
    }

    private function resolveFilePath(CmsItem $item, ?\App\Models\MediaAsset $mediaAsset): ?string
    {
        if ($mediaAsset !== null && $mediaAsset->filePath !== '') {
            return $mediaAsset->filePath;
        }

        if (!empty($item->textValue)) {
            return (string)$item->textValue;
        }

        return null;
    }

    private function resolveInputType(CmsItem $item): string
    {
        $type = $item->itemType->value;
        $inputType = CmsContentLimits::getInputType($type);

        // Certain TEXT keys (e.g. long descriptions) are edited via TinyMCE instead of a plain input
        if (strtoupper($type) === 'TEXT' && CmsContentLimits::textKeyUsesTinyMce($item->itemKey)) {
            return 'tinymce';
        }

        return $inputType;
    }

    private function buildEnrichedItem(CmsItem $item, ?\App\Models\MediaAsset $mediaAsset, ?string $resolvedFilePath, string $inputType): CmsItemEditData
    {
        $type = $item->itemType->value;

        return new CmsItemEditData(
            itemId: $item->cmsItemId,
            itemKey: $item->itemKey,
            displayName: $item->itemKey,
            type: $type,
            typeLabel: CmsContentLimits::getLabelForType($type),
            inputType: $inputType,
            maxChars: CmsContentLimits::getCharLimitForType($type),
            value: $this->getItemValue($item),
            mediaAssetId: $item->mediaAssetId,
            mediaAsset: $this->buildMediaAssetData($item, $mediaAsset, $resolvedFilePath, $type),
        );
    }

    private function buildMediaAssetData(CmsItem $item, ?\App\Models\MediaAsset $mediaAsset, ?string $resolvedFilePath, string $type): ?CmsMediaAssetData
    {
        if ($mediaAsset !== null) {
            return $this->mediaAssetDataFromAsset($mediaAsset);
        }
        if ($resolvedFilePath !== null && strtoupper($type) === 'IMAGE_PATH') {
            return $this->mediaAssetDataFromFilePath($item->itemKey, $resolvedFilePath);
        }
        return null;
    }

    private function mediaAssetDataFromAsset(\App\Models\MediaAsset $mediaAsset): CmsMediaAssetData
    {
        return new CmsMediaAssetData(
            filePath: $mediaAsset->filePath,
            originalFileName: $mediaAsset->originalFileName,
            altText: $mediaAsset->altText,
        );
    }

    private function mediaAssetDataFromFilePath(string $itemKey, string $filePath): CmsMediaAssetData
    {
        return new CmsMediaAssetData(
            filePath: $filePath,
            originalFileName: basename($filePath),
            altText: $itemKey,
        );
    }

    /**
     * Extracts the display value for the CMS editor. HTML items use htmlValue directly;
     * TEXT items that were accidentally stored with HTML tags get stripped back to plain text.
     */
    private function getItemValue(CmsItem $item): string
    {
        $type = strtoupper($item->itemType->value);

        if ($type === 'HTML') {
            return (string)($item->htmlValue ?? '');
        }

        $value = (string)($item->textValue ?? '');
        if ($type === 'TEXT' && $value !== '' && preg_match('/<[^>]+>/', $value) === 1) {
            return trim(strip_tags(html_entity_decode($value)));
        }

        return $value;
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
     * @param CmsSectionEditData[] $sections
     */
    private function extractFirstEventId(array $sections): ?int
    {
        foreach ($sections as $section) {
            if (preg_match('/^event_(\d+)$/', $section->sectionKey, $matches) === 1) {
                return (int)$matches[1];
            }
        }

        return null;
    }

    /**
     * @param CmsSectionEditData[] $sections
     */
    private function extractFirstEventDisplayName(array $sections): ?string
    {
        foreach ($sections as $section) {
            if (!str_starts_with($section->sectionKey, 'event_')) {
                continue;
            }

            $displayName = trim($section->displayName);
            if ($displayName !== '') {
                return $displayName;
            }
        }

        return null;
    }

    private function toSlug(string $value): string
    {
        $lower = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $lower);
        return trim((string)$slug, '-');
    }

    /**
     * Strips HTML for character counting.
     */
    private function stripHtmlForCount(string $value): string
    {
        return strip_tags(html_entity_decode($value));
    }
}
