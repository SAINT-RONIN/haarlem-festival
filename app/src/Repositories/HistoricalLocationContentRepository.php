<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\HistoricalLocationContentMapper;
use App\DTOs\Cms\HistoricalLocationFactsContent;
use App\DTOs\Cms\HistoricalLocationHeroContent;
use App\DTOs\Cms\HistoricalLocationIntroContent;
use App\DTOs\Cms\HistoricalLocationSignificanceContent;

class HistoricalLocationContentRepository extends BaseContentRepository implements Interfaces\IHistoricalLocationContentRepository
{
    public function findHeroContent(string $pageSlug, string $sectionKey): HistoricalLocationHeroContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapHero($raw);
    }

    // Re-maps already-fetched raw hero data (avoids double-fetching after existence check).
    public function mapHeroFromRaw(array $raw): HistoricalLocationHeroContent
    {
        return HistoricalLocationContentMapper::mapHero($raw);
    }

    public function findIntroContent(string $pageSlug, string $sectionKey): HistoricalLocationIntroContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapIntro($raw);
    }

    public function findFactsContent(string $pageSlug, string $sectionKey): HistoricalLocationFactsContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapFacts($raw);
    }

    public function findSignificanceContent(string $pageSlug, string $sectionKey): HistoricalLocationSignificanceContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoricalLocationContentMapper::mapSignificance($raw);
    }
}
