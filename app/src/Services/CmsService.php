<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CmsItem;
use App\Models\CmsSection;
use App\Models\MediaAsset;
use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\ICmsService;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

class CmsService implements ICmsService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;
    private SessionService $sessionService;

    /** @var array<string, int|null> In-memory cache for page slug → ID lookups */
    private array $pageIdCache = [];

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->sessionService = new SessionService();
    }

    public function getHomePageContent(): array
    {
        $pageId = $this->getPageIdBySlug('home');
        if ($pageId === null) {
            return [];
        }

        $sections = $this->cmsRepository->findSections(['cmsPageId' => $pageId]);
        $items = $this->cmsRepository->findItems(['cmsPageId' => $pageId]);

        $assetMap = $this->batchFetchMediaAssets($items);
        $itemsBySection = $this->indexItemsBySectionId($items);
        $content = [];

        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $sectionData = [];

            foreach ($itemsBySection[$section->cmsSectionId] ?? [] as $item) {
                /** @var CmsItem $item */
                $sectionData[$item->itemKey] = $this->resolveItemValue($item, $assetMap);
            }

            $content[$section->sectionKey] = $sectionData;
        }

        return $content;
    }

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $pageId = $this->getPageIdBySlug($pageSlug);
        if ($pageId === null) {
            return [];
        }

        $items = $this->cmsRepository->findItems([
            'cmsPageId' => $pageId,
            'sectionKey' => $sectionKey,
        ]);

        $assetMap = $this->batchFetchMediaAssets($items);
        $content = [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            $content[$item->itemKey] = $this->resolveItemValue($item, $assetMap);
        }

        return $content;
    }

    public function getHeroSectionContent(string $pageSlug): array
    {
        return $this->getSectionContent($pageSlug, 'hero_section');
    }

    public function buildHeroData(string $pageSlug, string $currentPage): HeroData
    {
        return HeroData::fromCms($this->getHeroSectionContent($pageSlug), $currentPage);
    }

    /**
     * @return array{content: array, isLoggedIn: bool}
     */
    public function getGlobalUiContent(): array
    {
        return [
            'content' => $this->getSectionContent('home', 'global_ui'),
            'isLoggedIn' => $this->sessionService->isLoggedIn(),
        ];
    }

    public function buildGlobalUiData(): GlobalUiData
    {
        $globalUiContent = $this->getGlobalUiContent();

        return GlobalUiData::fromCms($globalUiContent['content'], $globalUiContent['isLoggedIn']);
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
     *
     * @param array<int, MediaAsset> $assetMap Pre-fetched media assets keyed by ID
     */
    private function resolveItemValue(CmsItem $item, array $assetMap): ?string
    {
        $mediaAssetId = $item->mediaAssetId;
        if ($mediaAssetId !== null && $mediaAssetId > 0) {
            $asset = $assetMap[$mediaAssetId] ?? null;
            if ($asset !== null && $asset->filePath !== '') {
                return $asset->filePath;
            }
        }

        $value = $item->textValue ?? $item->htmlValue ?? null;
        return is_string($value) ? $value : null;
    }

    private function getPageIdBySlug(string $slug): ?int
    {
        if (array_key_exists($slug, $this->pageIdCache)) {
            return $this->pageIdCache[$slug];
        }

        $rows = $this->cmsRepository->findPages(['slug' => $slug]);
        $pageId = $rows !== [] ? (int)$rows[0]['CmsPageId'] : null;

        $this->pageIdCache[$slug] = $pageId;
        return $pageId;
    }

    /**
     * @param CmsItem[] $items
     * @return array<int, list<CmsItem>>
     */
    private function indexItemsBySectionId(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item->cmsSectionId][] = $item;
        }

        return $indexed;
    }
}
