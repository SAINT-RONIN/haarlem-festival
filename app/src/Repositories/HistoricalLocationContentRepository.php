<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\HistoricalLocationContentMapper;
use App\Models\HistoricalLocationFactsContent;
use App\Models\HistoricalLocationHeroContent;
use App\Models\HistoricalLocationIntroContent;
use App\Models\HistoricalLocationSignificanceContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to HistoricalLocation CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to HistoricalLocationContentMapper.
 */
class HistoricalLocationContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the location-specific hero content. */
    public function findHeroContent(string $pageSlug, string $sectionKey): HistoricalLocationHeroContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapHero($raw);
    }

    /**
     * Maps an already-fetched raw hero array to a location hero model.
     *
     * Used when the hero raw data is shared with the generic HeroSectionContent
     * and has already been retrieved to check for page existence.
     */
    public function mapHeroFromRaw(array $raw): HistoricalLocationHeroContent
    {
        return HistoricalLocationContentMapper::mapHero($raw);
    }

    /** Fetches the location intro content. */
    public function findIntroContent(string $pageSlug, string $sectionKey): HistoricalLocationIntroContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapIntro($raw);
    }

    /** Fetches the location facts content. */
    public function findFactsContent(string $pageSlug, string $sectionKey): HistoricalLocationFactsContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapFacts($raw);
    }

    /** Fetches the location significance content. */
    public function findSignificanceContent(string $pageSlug, string $sectionKey): HistoricalLocationSignificanceContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapSignificance($raw);
    }
}
