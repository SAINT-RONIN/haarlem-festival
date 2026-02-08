<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;

class CmsService
{
    private CmsRepository $cmsRepository;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
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
                $value = $item['TextValue'] ?? $item['HtmlValue'] ?? null;
                $sectionData[$item['ItemKey']] = $value;
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
            $value = $item['TextValue'] ?? $item['HtmlValue'] ?? null;
            $content[$item['ItemKey']] = $value;
        }

        return $content;
    }
}
