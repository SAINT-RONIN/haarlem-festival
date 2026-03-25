<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoricalLocationPageConstants;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\Models\HeroSectionContent;
use App\Models\HistoricalLocationFactsContent;
use App\Models\HistoricalLocationHeroContent;
use App\Models\HistoricalLocationIntroContent;
use App\Models\HistoricalLocationPageData;
use App\Models\HistoricalLocationSignificanceContent;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IHistoricalLocationService;

class HistoricalLocationService implements IHistoricalLocationService
{
    public function __construct(
        private readonly ICmsPageContentService $cmsService,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    public function getHistoralLocationPageData(string $name): HistoricalLocationPageData
    {
        $heroRaw = $this->cmsService->getSectionContent($name, HistoricalLocationPageConstants::SECTION_HERO);

        if (empty($heroRaw)) {
            throw new HistoricalLocationNotFoundException($name);
        }

        return $this->buildPageData($name, $heroRaw);
    }

    /** @param array<string, ?string> $heroRaw */
    private function buildPageData(string $slug, array $heroRaw): HistoricalLocationPageData
    {
        return new HistoricalLocationPageData(
            heroSection:         HeroSectionContent::fromRawArray($heroRaw),
            locationHeroSection: HistoricalLocationHeroContent::fromRawArray($heroRaw),
            introSection:        HistoricalLocationIntroContent::fromRawArray($this->cmsService->getSectionContent($slug, HistoricalLocationPageConstants::SECTION_INTRO)),
            factsSection:        HistoricalLocationFactsContent::fromRawArray($this->cmsService->getSectionContent($slug, HistoricalLocationPageConstants::SECTION_FACTS)),
            significanceSection: HistoricalLocationSignificanceContent::fromRawArray($this->cmsService->getSectionContent($slug, HistoricalLocationPageConstants::SECTION_SIGNIFICANCE)),
            globalUiContent:     $this->globalUiLoader->load(),
        );
    }
}
