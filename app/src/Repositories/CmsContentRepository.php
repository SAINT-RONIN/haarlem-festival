<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CmsItem;
use App\Models\CmsItemFilter;
use App\Models\CmsPageFilter;
use App\Models\CmsSection;
use App\Models\CmsSectionFilter;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;

/**
 * High-level read-only facade over the CMS data (pages, sections, items).
 *
 * Resolves page slugs to structured content arrays suitable for views,
 * caches results per-request to avoid repeated queries, and batch-fetches
 * linked MediaAsset records to prevent N+1 lookups.
 */
class CmsContentRepository implements ICmsContentRepository
{
    /** @var array<string, int|null> In-memory cache for page slug → ID lookups */
    private array $pageIdCache = [];

    /** @var array<int, array<string, array<string, ?string>>> In-memory cache: pageId → sectionKey → itemKey → value */
    private array $pageContentCache = [];

    /** @var array<int, CmsSection[]> In-memory cache: pageId → sections (for ordered iteration) */
    private array $pageSectionsCache = [];

    public function __construct(
        private ICmsRepository $cmsRepository,
        private IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * Returns all CMS content for the home page, keyed by sectionKey then itemKey.
     *
     * @return array<string, array<string, ?string>>
     */
    public function getHomePageContent(): array
    {
        $pageId = $this->getPageIdBySlug('home');
        if ($pageId === null) {
            return [];
        }

        $sections = $this->loadPageContent($pageId);
        $content = [];

        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $content[$section->sectionKey] = $this->pageContentCache[$pageId][$section->sectionKey] ?? [];
        }

        return $content;
    }

    /**
     * Returns one section's items from a page, keyed by itemKey.
     *
     * @return array<string, ?string>
     */
    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $pageId = $this->getPageIdBySlug($pageSlug);
        if ($pageId === null) {
            return [];
        }

        $this->loadPageContent($pageId);
        return $this->pageContentCache[$pageId][$sectionKey] ?? [];
    }

    /**
     * Convenience shortcut to retrieve the "hero_section" of any page.
     *
     * @return array<string, ?string>
     */
    public function getHeroSectionContent(string $pageSlug): array
    {
        return $this->getSectionContent($pageSlug, 'hero_section');
    }

    /**
     * Loads all CMS items for a page in one query, groups them by sectionKey, and caches the result.
     * Returns the sections array so callers that need section order don't have to re-query.
     *
     * @return CmsSection[]
     */
    private function loadPageContent(int $pageId): array
    {
        if (isset($this->pageContentCache[$pageId])) {
            return $this->pageSectionsCache[$pageId];
        }

        $sections = $this->cmsRepository->findSections(new CmsSectionFilter(cmsPageId: $pageId));
        $sectionKeyById = $this->indexSectionKeysById($sections);
        $items = $this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId));
        $assetMap = $this->batchFetchMediaAssets($items);
        $grouped = [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            $sectionKey = $sectionKeyById[$item->cmsSectionId] ?? null;
            if ($sectionKey === null) {
                continue;
            }
            $grouped[$sectionKey][$item->itemKey] = $this->resolveItemValue($item, $assetMap);
        }

        $this->pageContentCache[$pageId] = $grouped;
        $this->pageSectionsCache[$pageId] = $sections;
        return $sections;
    }

    /**
     * Batch-fetches all MediaAssets referenced by the given CMS items in one query.
     *
     * @param CmsItem[] $items
     * @return array<int, MediaAsset> Keyed by MediaAssetId
     */
    private function batchFetchMediaAssets(array $items): array
    {
        $assetIds = [];
        foreach ($items as $item) {
            if ($item->mediaAssetId !== null && $item->mediaAssetId > 0) {
                $assetIds[] = $item->mediaAssetId;
            }
        }

        if ($assetIds === []) {
            return [];
        }

        return $this->mediaAssetRepository->findByIds($assetIds);
    }

    /**
     * Resolves the value to expose to views for a CMS item.
     * Priority: media asset file path > plain text > HTML > null.
     *
     * @param array<int, MediaAsset> $assetMap Pre-fetched media assets keyed by ID
     */
    private function resolveItemValue(CmsItem $item, array $assetMap): ?string
    {
        // Media asset takes highest priority (used for images/files)
        $mediaAssetId = $item->mediaAssetId;
        if ($mediaAssetId !== null && $mediaAssetId > 0) {
            $asset = $assetMap[$mediaAssetId] ?? null;
            if ($asset !== null && $asset->filePath !== '') {
                return $asset->filePath;
            }
        }

        if ($item->textValue !== null && $item->textValue !== '') {
            return $item->textValue;
        }

        if ($item->htmlValue !== null && $item->htmlValue !== '') {
            return $item->htmlValue;
        }

        return null;
    }

    private function getPageIdBySlug(string $slug): ?int
    {
        if (array_key_exists($slug, $this->pageIdCache)) {
            return $this->pageIdCache[$slug];
        }

        $rows = $this->cmsRepository->findPages(new CmsPageFilter(slug: $slug));
        $pageId = $rows !== [] ? $rows[0]->cmsPageId : null;

        $this->pageIdCache[$slug] = $pageId;
        return $pageId;
    }

    /**
     * @param CmsSection[] $sections
     * @return array<int, string> Keyed by cmsSectionId, value is sectionKey
     */
    private function indexSectionKeysById(array $sections): array
    {
        $indexed = [];
        foreach ($sections as $section) {
            $indexed[$section->cmsSectionId] = $section->sectionKey;
        }

        return $indexed;
    }
}
