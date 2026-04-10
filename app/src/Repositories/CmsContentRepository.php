<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CmsItem;
use App\DTOs\Domain\Filters\CmsItemFilter;
use App\DTOs\Domain\Filters\CmsPageFilter;
use App\Models\CmsSection;
use App\DTOs\Domain\Filters\CmsSectionFilter;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;

// Read-only facade over CMS data. Caches per-request and batch-fetches
// MediaAssets to prevent N+1 lookups.
class CmsContentRepository implements ICmsContentRepository
{
    /** @var array<string, int|null> slug -> page ID */
    private array $pageIdCache = [];

    /** @var array<int, array<string, array<string, ?string>>> pageId -> sectionKey -> itemKey -> value */
    private array $pageContentCache = [];

    /** @var array<int, CmsSection[]> pageId -> sections (for ordered iteration) */
    private array $pageSectionsCache = [];

    public function __construct(
        private ICmsRepository $cmsRepository,
        private IMediaAssetRepository $mediaAssetRepository,
    ) {}

    public function getHomePageContent(): array
    {
        $pageId = $this->getPageIdBySlug('home');
        if ($pageId === null) {
            return [];
        }

        $sections = $this->loadPageContent($pageId);
        $content = [];

        foreach ($sections as $section) {
            $content[$section->sectionKey] = $this->pageContentCache[$pageId][$section->sectionKey] ?? [];
        }

        return $content;
    }

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $pageId = $this->getPageIdBySlug($pageSlug);
        if ($pageId === null) {
            return [];
        }

        $this->loadPageContent($pageId);
        return $this->pageContentCache[$pageId][$sectionKey] ?? [];
    }

    public function getHeroSectionContent(string $pageSlug): array
    {
        return $this->getSectionContent($pageSlug, 'hero_section');
    }

    // Loads all CMS items for a page in one query, groups by section, caches.
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

    // Priority: media asset file path > text > HTML > null
    private function resolveItemValue(CmsItem $item, array $assetMap): ?string
    {
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

    /** @return array<int, string> */
    private function indexSectionKeysById(array $sections): array
    {
        $indexed = [];
        foreach ($sections as $section) {
            $indexed[$section->cmsSectionId] = $section->sectionKey;
        }

        return $indexed;
    }
}
