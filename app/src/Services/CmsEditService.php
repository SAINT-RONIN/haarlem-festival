<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;
use App\Utils\CmsContentLimits;

/**
 * Service for CMS page editing operations.
 *
 * Handles business logic for loading, validating, and saving
 * CMS page content.
 */
class CmsEditService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
    }

    /**
     * Gets a page with all its sections and items for editing.
     *
     * @return array|null Array with 'page', 'sections' keys or null if not found
     */
    public function getPageForEditing(int $pageId): ?array
    {
        $page = $this->cmsRepository->getPageById($pageId);
        if (!$page) {
            return null;
        }

        $sections = $this->cmsRepository->getSectionsByPageId($pageId);
        $sectionsWithItems = [];

        foreach ($sections as $section) {
            $items = $this->cmsRepository->getItemsBySectionId($section['CmsSectionId']);
            $enrichedItems = $this->enrichItemsWithMetadata($items);

            $sectionsWithItems[] = [
                'sectionId' => $section['CmsSectionId'],
                'sectionKey' => $section['SectionKey'],
                'displayName' => $this->formatSectionName($section['SectionKey']),
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
     * @param array $items Array of item updates: [itemId => ['value' => '', 'type' => '']]
     * @return array ['success' => bool, 'errors' => array]
     */
    public function updatePageItems(int $pageId, array $items): array
    {
        $errors = [];
        $updatedCount = 0;

        foreach ($items as $itemId => $itemData) {
            $itemId = (int)$itemId;
            $item = $this->cmsRepository->getItemById($itemId);

            if (!$item) {
                $errors[] = "Item ID {$itemId} not found";
                continue;
            }

            if (!$this->itemBelongsToPage($item, $pageId)) {
                $errors[] = "Item ID {$itemId} does not belong to this page";
                continue;
            }

            $type = $item['ItemType'];
            $value = $itemData['value'] ?? '';

            $validationError = $this->validateItemValue($value, $type, $item['ItemKey']);
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
     * Checks if an item belongs to the given page.
     */
    private function itemBelongsToPage(array $item, int $pageId): bool
    {
        $sections = $this->cmsRepository->getSectionsByPageId($pageId);
        $sectionIds = array_column($sections, 'CmsSectionId');
        return in_array($item['CmsSectionId'], $sectionIds, true);
    }

    /**
     * Enriches items with display metadata.
     */
    private function enrichItemsWithMetadata(array $items): array
    {
        $enriched = [];

        foreach ($items as $item) {
            $type = $item['ItemType'];
            $mediaAsset = $this->getMediaAssetInfo($item['MediaAssetId']);

            $resolvedFilePath = null;
            if (is_array($mediaAsset) && !empty($mediaAsset['FilePath'])) {
                $resolvedFilePath = (string)$mediaAsset['FilePath'];
            } elseif (!empty($item['TextValue'])) {
                $resolvedFilePath = (string)$item['TextValue'];
            }

            if ($resolvedFilePath !== null && strtoupper($type) === 'IMAGE_PATH' && !$mediaAsset) {
                $mediaAsset = [
                    'FilePath' => $resolvedFilePath,
                    'OriginalFileName' => basename($resolvedFilePath),
                    'AltText' => $this->formatItemKeyName($item['ItemKey'])
                ];
            }

            $inputType = CmsContentLimits::getInputType($type);
            if (strtoupper((string)$type) === 'TEXT' && CmsContentLimits::textKeyUsesTinyMce((string)$item['ItemKey'])) {
                $inputType = 'tinymce';
            }

            $enriched[] = [
                'itemId' => $item['CmsItemId'],
                'itemKey' => $item['ItemKey'],
                'displayName' => $this->formatItemKeyName($item['ItemKey']),
                'type' => $type,
                'typeLabel' => CmsContentLimits::getLabelForType($type),
                'inputType' => $inputType,
                'maxChars' => CmsContentLimits::getCharLimitForType($type),
                'value' => $this->getItemValue($item),
                'mediaAssetId' => $item['MediaAssetId'],
                'mediaAsset' => $mediaAsset,
            ];
        }

        return $enriched;
    }

    /**
     * Gets the appropriate value from an item.
     */
    private function getItemValue(array $item): string
    {
        $type = strtoupper((string)($item['ItemType'] ?? ''));

        if ($type === 'HTML') {
            return (string)($item['HtmlValue'] ?? '');
        }

        $value = (string)($item['TextValue'] ?? '');
        if ($type === 'TEXT' && $value !== '' && preg_match('/<[^>]+>/', $value) === 1) {
            return trim(strip_tags(html_entity_decode($value)));
        }

        return $value;
    }

    /**
     * Gets media asset info if exists.
     */
    private function getMediaAssetInfo(?int $mediaAssetId): ?array
    {
        if (!$mediaAssetId) {
            return null;
        }
        return $this->mediaAssetRepository->findById($mediaAssetId);
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
     * Strips HTML for character counting.
     */
    private function stripHtmlForCount(string $value): string
    {
        return strip_tags(html_entity_decode($value));
    }
}

