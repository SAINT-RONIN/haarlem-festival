<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;

class CmsService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
    }

    public function getHomePageContent(): array
    {
        $page = $this->cmsRepository->getPageBySlug('home');
        if (!$page) {
            return [];
        }

        $sections = $this->cmsRepository->getSectionsByPageId((int)$page['CmsPageId']);
        $content = [];

        foreach ($sections as $section) {
            $items = $this->cmsRepository->getItemsBySectionId((int)$section['CmsSectionId']);
            $sectionData = [];

            foreach ($items as $item) {
                $sectionData[$item['ItemKey']] = $this->getItemValue($item);
            }

            $content[$section['SectionKey']] = $sectionData;
        }

        return $content;
    }

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $page = $this->cmsRepository->getPageBySlug($pageSlug);
        if (!$page) {
            return [];
        }

        $items = $this->cmsRepository->getItemsBySectionKey((int)$page['CmsPageId'], $sectionKey);
        $content = [];

        foreach ($items as $item) {
            $content[$item['ItemKey']] = $this->getItemValue($item);
        }

        return $content;
    }

    /**
     * Gets the value from a CMS item based on its type.
     * For MEDIA items, returns the file path from MediaAsset.
     */
    private function getItemValue(array $item): ?string
    {
        // Handle MEDIA type - get file path from MediaAsset
        if (strtoupper($item['ItemType']) === 'MEDIA' && !empty($item['MediaAssetId'])) {
            $mediaAsset = $this->mediaAssetRepository->findById((int)$item['MediaAssetId']);
            return $mediaAsset['FilePath'] ?? null;
        }

        // Handle text/html types
        return $item['TextValue'] ?? $item['HtmlValue'] ?? null;
    }
}
