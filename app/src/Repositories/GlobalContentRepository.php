<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to shared CMS content sections used across all pages.
 */
class GlobalContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    public function findHeroContent(string $pageSlug): HeroSectionContent
    {
        return HeroSectionContent::fromRawArray(
            $this->cmsContent->getHeroSectionContent($pageSlug),
        );
    }

    public function findGlobalUiContent(string $pageSlug, string $sectionKey): GlobalUiContent
    {
        return GlobalUiContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }
}
