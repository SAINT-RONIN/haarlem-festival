<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Models\CmsItem;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Repositories\CmsRepository;
use App\Repositories\EventRepository;
use App\Repositories\MediaAssetRepository;
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
        private CmsRepository $cmsRepository,
        private MediaAssetRepository $mediaAssetRepository,
        private EventRepository $eventRepository,
    ) {
    }

    /**
     * Gets a page with all its sections and items for editing.
     *
     * @return array|null Array with 'page', 'sections' keys or null if not found
     */
    public function getPageForEditing(int $pageId): ?array
    {
        $pages = $this->cmsRepository->findPages(['cmsPageId' => $pageId]);
        $page = $pages[0] ?? null;
        if ($page === null) {
            return null;
        }

        $sections = $this->cmsRepository->findSections(['cmsPageId' => $pageId]);
        $items = $this->cmsRepository->findItems(['cmsPageId' => $pageId]);
        $itemsBySection = $this->groupItemsBySection($items);
        $sectionsWithItems = [];

        $eventNameMap = [];
        $pageSlug = $page->slug;
        if ($pageSlug === 'storytelling-detail') {
            $eventNameMap = $this->buildEventNameMap(EventTypeId::Storytelling->value);
        }

        if ($pageSlug === 'jazz-artist-detail') {
            $eventNameMap = $this->buildEventNameMap(EventTypeId::Jazz->value);
        }

        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $sectionItems = $itemsBySection[$section->cmsSectionId] ?? [];
            $enrichedItems = $this->enrichItemsWithMetadata($sectionItems);

            $sectionsWithItems[] = [
                'sectionId' => $section->cmsSectionId,
                'sectionKey' => $section->sectionKey,
                'displayName' => $this->resolveDisplayName($section->sectionKey, $eventNameMap),
                'items' => $enrichedItems
            ];
        }

        return [
            'page' => $page,
            'sections' => $sectionsWithItems
        ];
    }

    /**
     * Updates multiple CMS items from form submission.
     *
     * @param int $pageId The page ID for validation
     * @param array $items Array of item updates: [itemId => value_string]
     * @return array ['success' => bool, 'errors' => array]
     */
    public function updatePageItems(int $pageId, array $items): array
    {
        $errors = [];
        $updatedCount = 0;
        $pageItems = $this->cmsRepository->findItems(['cmsPageId' => $pageId]);
        $pageItemsById = [];
        foreach ($pageItems as $pageItem) {
            $pageItemsById[$pageItem->cmsItemId] = $pageItem;
        }

        foreach ($items as $itemId => $rawValue) {
            $itemId = (int)$itemId;
            $item = $pageItemsById[$itemId] ?? null;

            if (!$item) {
                $errors[] = "Item ID {$itemId} not found";
                continue;
            }

            $type = $item->itemType->value;
            $itemData = ['value' => $rawValue];
            $value = $itemData['value'] ?? '';

            $validationError = $this->validateItemValue($value, $type, $item->itemKey);
            if ($validationError) {
                $errors[] = $validationError;
                continue;
            }

            $updateData = $this->prepareUpdateData($value, $type);
            if ($this->cmsRepository->updateItem($itemId, $updateData)) {
                $updatedCount++;
            }
        }

        return [
            'success' => empty($errors),
            'updatedCount' => $updatedCount,
            'errors' => $errors
        ];
    }

    /**
     * Updates a single CMS item's media asset.
     */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool
    {
        return $this->cmsRepository->updateItemMediaAsset($itemId, $mediaAssetId);
    }

    /**
     * Builds a route-aware preview URL for CMS page edit screens.
     *
     * @param array<int, array<string, mixed>> $sections
     */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string
    {
        $slug = $page->slug;

        if ($slug === 'home') {
            return '/';
        }

        if ($slug === 'storytelling-detail') {
            $eventId = $this->extractFirstEventId($sections);
            return $eventId !== null ? '/storytelling/' . $eventId : '/storytelling';
        }

        if ($slug === 'jazz-artist-detail') {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null ? '/jazz/' . $this->toSlug($eventName) : '/jazz';
        }

        if ($slug === 'restaurant-detail') {
            $eventId = $this->extractFirstEventId($sections);
            return $eventId !== null ? '/restaurant/' . $eventId : '/restaurant';
        }

        return '/' . $slug;
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
            $label = $this->formatItemKeyName($itemKey);
            return "{$label} exceeds maximum of {$maxChars} characters";
        }

        return null;
    }

    /**
     * Prepares data array for repository update.
     */
    private function prepareUpdateData(string $value, string $type): array
    {
        $normalizedType = strtoupper($type);

        if ($normalizedType === 'HTML') {
            return ['HtmlValue' => $value, 'TextValue' => null];
        }

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
     * Enriches items with display metadata.
     *
     * @param CmsItem[] $items
     */
    private function enrichItemsWithMetadata(array $items): array
    {
        $mediaAssets = $this->loadMediaAssetsForItems($items);
        $enriched = [];

        foreach ($items as $item) {
            $mediaAsset = $this->resolveMediaAsset($item, $mediaAssets);
            $resolvedFilePath = $this->resolveFilePath($item, $mediaAsset);
            $inputType = $this->resolveInputType($item);
            $enriched[] = $this->buildEnrichedItem($item, $mediaAsset, $resolvedFilePath, $inputType);
        }

        return $enriched;
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

        if (strtoupper($type) === 'TEXT' && CmsContentLimits::textKeyUsesTinyMce($item->itemKey)) {
            return 'tinymce';
        }

        return $inputType;
    }

    private function buildEnrichedItem(CmsItem $item, ?\App\Models\MediaAsset $mediaAsset, ?string $resolvedFilePath, string $inputType): array
    {
        $type = $item->itemType->value;
        $mediaAssetArray = $this->buildMediaAssetArray($item, $mediaAsset, $resolvedFilePath, $type);

        return [
            'itemId' => $item->cmsItemId,
            'itemKey' => $item->itemKey,
            'displayName' => $this->formatItemKeyName($item->itemKey),
            'type' => $type,
            'typeLabel' => CmsContentLimits::getLabelForType($type),
            'inputType' => $inputType,
            'maxChars' => CmsContentLimits::getCharLimitForType($type),
            'value' => $this->getItemValue($item),
            'mediaAssetId' => $item->mediaAssetId,
            'mediaAsset' => $mediaAssetArray,
        ];
    }

    private function buildMediaAssetArray(CmsItem $item, ?\App\Models\MediaAsset $mediaAsset, ?string $resolvedFilePath, string $type): ?array
    {
        if ($mediaAsset !== null) {
            return [
                'FilePath' => $mediaAsset->filePath,
                'OriginalFileName' => $mediaAsset->originalFileName,
                'AltText' => $mediaAsset->altText,
            ];
        }

        if ($resolvedFilePath !== null && strtoupper($type) === 'IMAGE_PATH') {
            return [
                'FilePath' => $resolvedFilePath,
                'OriginalFileName' => basename($resolvedFilePath),
                'AltText' => $this->formatItemKeyName($item->itemKey),
            ];
        }

        return null;
    }

    /**
     * Gets the appropriate value from an item.
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
     */
    private function buildEventNameMap(int $eventTypeId): array
    {
        $events = $this->eventRepository->findEvents(['eventTypeId' => $eventTypeId]);
        $map = [];
        foreach ($events as $event) {
            $map['event_' . $event->eventId] = $event->title;
        }
        return $map;
    }

    /**
     * Resolves a section display name, using the event name map when available.
     */
    private function resolveDisplayName(string $sectionKey, array $eventNameMap): string
    {
        if (!empty($eventNameMap) && isset($eventNameMap[$sectionKey])) {
            return $eventNameMap[$sectionKey];
        }
        return $this->formatSectionName($sectionKey);
    }

    /**
     * Formats a section key into a display name.
     * e.g., 'hero_section' -> 'Hero Section'
     */
    private function formatSectionName(string $sectionKey): string
    {
        $name = str_replace('_', ' ', $sectionKey);
        return ucwords($name);
    }

    /**
     * Formats an item key into a display name.
     * e.g., 'hero_main_title' -> 'Hero Main Title'
     */
    private function formatItemKeyName(string $itemKey): string
    {
        $name = str_replace('_', ' ', $itemKey);
        return ucwords($name);
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     */
    private function extractFirstEventId(array $sections): ?int
    {
        foreach ($sections as $section) {
            $sectionKey = (string)($section['sectionKey'] ?? '');
            if (preg_match('/^event_(\d+)$/', $sectionKey, $matches) === 1) {
                return (int)$matches[1];
            }
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     */
    private function extractFirstEventDisplayName(array $sections): ?string
    {
        foreach ($sections as $section) {
            $sectionKey = (string)($section['sectionKey'] ?? '');
            if (!str_starts_with($sectionKey, 'event_')) {
                continue;
            }

            $displayName = trim((string)($section['displayName'] ?? ''));
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
