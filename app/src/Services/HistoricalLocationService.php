<?php

declare(strict_types=1);

namespace App\Services;


use App\Constants\SharedSectionKeys;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\DTOs\Domain\Pages\HistoricalLocationPageData;
use App\Mappers\HistoricalLocationContentMapper;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IHistoricalLocationService;

class HistoricalLocationService extends BaseContentService implements IHistoricalLocationService
{
    public function __construct(
        private ICmsContentRepository $cmsContentRepository,
        IGlobalContentRepository $globalContentRepo,
    ) {
        parent::__construct($globalContentRepo);
    }

    public function getHistoralLocationPageData(string $name): HistoricalLocationPageData
    {
        $heroRaw = $this->cmsContentRepository->getSectionContent($name, SharedSectionKeys::SECTION_HERO);

        if (empty($heroRaw)) {
            throw new HistoricalLocationNotFoundException($name);
        }

        return $this->buildPageData($name, $heroRaw);
    }

    /** @param array<string, ?string> $heroRaw */
    private function buildPageData(string $slug, array $heroRaw): HistoricalLocationPageData
    {
        $rawContent = $this->cmsContentRepository->getPageContent($slug);
        return new HistoricalLocationPageData(
            heroSection: $this->globalContentRepo->mapHeroFromRaw($heroRaw),
            locationHeroSection: HistoricalLocationContentMapper::mapHero($rawContent['hero_section']),
            introSection: HistoricalLocationContentMapper::mapIntro($rawContent['intro_section']),
            factsSection: HistoricalLocationContentMapper::mapFacts($rawContent['facts_section']),
            significanceSection: HistoricalLocationContentMapper::mapSignificance($rawContent['significance_section']),
            globalUiContent: $this->loadGlobalUi(),
        );
    }
}
