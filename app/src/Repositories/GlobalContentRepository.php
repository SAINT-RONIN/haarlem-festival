<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\GlobalContentMapper;
use App\Content\GlobalUiContent;
use App\Content\GradientSectionContent;
use App\Content\HeroSectionContent;
use App\Content\IntroSectionContent;

/**
 * Provides typed access to shared/global CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to GlobalContentMapper so callers receive typed models instead of raw arrays.
 */
class GlobalContentRepository extends BaseContentRepository implements Interfaces\IGlobalContentRepository
{
    /** Fetches the global UI navigation/labels content. */
    public function findGlobalUiContent(string $pageSlug, string $sectionKey): GlobalUiContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
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
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapHero($raw);
    }

    /**
     * Maps an already-fetched raw hero array to a HeroSectionContent model.
     *
     * Used when the raw data has already been retrieved for an existence check
     * and should not be fetched a second time.
     */
    public function mapHeroFromRaw(array $raw): HeroSectionContent
    {
        return GlobalContentMapper::mapHero($raw);
    }

    /** Fetches the gradient section content for a given page and section key. */
    public function findGradientContent(string $pageSlug, string $sectionKey): GradientSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapGradient($raw);
    }

    /** Fetches the intro section content for a given page and section key. */
    public function findIntroContent(string $pageSlug, string $sectionKey): IntroSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapIntro($raw);
    }
}
