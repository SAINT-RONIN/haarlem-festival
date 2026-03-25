<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\GlobalContentMapper;
use App\Models\GlobalUiContent;
use App\Models\GradientSectionContent;
use App\Models\HeroSectionContent;
use App\Models\IntroSectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to shared/global CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to GlobalContentMapper so callers receive typed models instead of raw arrays.
 */
class GlobalContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the global UI navigation/labels content. */
    public function findGlobalUiContent(string $pageSlug, string $sectionKey): GlobalUiContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapGlobalUi($raw);
    }

    /** Fetches the hero section content for a given page. */
    public function findHeroContent(string $pageSlug): HeroSectionContent
    {
        $raw = $this->cmsContent->getHeroSectionContent($pageSlug);
        return GlobalContentMapper::mapHero($raw);
    }

    /** Fetches hero content using a custom section key (not the default hero key). */
    public function findHeroContentBySection(string $pageSlug, string $sectionKey): HeroSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapHero($raw);
    }

    /** Fetches the gradient section content for a given page and section key. */
    public function findGradientContent(string $pageSlug, string $sectionKey): GradientSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapGradient($raw);
    }

    /** Fetches the intro section content for a given page and section key. */
    public function findIntroContent(string $pageSlug, string $sectionKey): IntroSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapIntro($raw);
    }
}
