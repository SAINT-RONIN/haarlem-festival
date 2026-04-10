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

// Loads CMS pages for editing and saves content changes.
// Sections/items are fetched in two queries (not N+1) and assembled in memory.
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

    /** @return CmsSectionEditData[] */
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

    // Sections for deleted/inactive events are silently skipped.
    /** @return CmsSectionEditData[] */
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

    /** @return array<string, string> */
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

    /** @param array<int|string, mixed> $items [itemId => value_string] */
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

    /** @param array<int, CmsItem> $indexedItems */
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

    /** @return array<int, CmsItem> */
    private function indexPageItemsById(int $pageId): array
    {
        $pageItems = $this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId));
        $indexed = [];
        foreach ($pageItems as $pageItem) {
            $indexed[$pageItem->cmsItemId] = $pageItem;
        }
        return $indexed;
    }

    /** @throws CmsOperationException */
    public function updateItemImage(int $itemId, int $mediaAssetId): bool
    {
        try {
            return $this->cmsRepository->updateItemMediaAsset($itemId, $mediaAssetId);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update item image.', 0, $error);
        }
    }

    /** @param CmsSectionEditData[] $sections */
    public function resolvePreviewUrl(CmsPage $page, array $sections): string
    {
        return $this->previewUrlResolver->resolve($page, $sections);
    }

    // HTML tags are stripped before counting so markup doesn't inflate the count.
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

    /** @return array{HtmlValue: ?string, TextValue: ?string} */
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

    /** @return array<int, list<CmsItem>> */
    private function groupItemsBySection(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->cmsSectionId][] = $item;
        }

        return $grouped;
    }

    /** @return CmsItem[] */
    private function sortHeroImageFirst(array $items): array
    {
        // The <=> comparison returns 1 when b is hero_image, pushing it to the front.
        // When neither item is hero_image the order stays unchanged.
        usort($items, fn(CmsItem $a, CmsItem $b) => ($b->itemKey === 'hero_image') <=> ($a->itemKey === 'hero_image'));
        return $items;
    }

    /** @return array<string, string> */
    private function buildEventNameMap(int $eventTypeId): array
    {
        $events = $this->eventRepository->findEvents(new EventFilter(eventTypeId: $eventTypeId, isActive: true));
        $map = [];
        foreach ($events as $event) {
            $map['event_' . $event->eventId] = $event->title;
        }
        return $map;
    }

    private function stripHtmlForCount(string $value): string
    {
        return strip_tags(html_entity_decode($value));
    }
}


