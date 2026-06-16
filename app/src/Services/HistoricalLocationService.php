<?php

declare(strict_types=1);

namespace App\Services;


use App\Constants\SharedSectionKeys;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\DTOs\Domain\Pages\HistoricalLocationPageData;
use App\Mappers\HistoricalLocationMapper;
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

    /**
     * @param string $pageSlug
     * @return HistoricalLocationPageData
     */
    public function getHistoralLocationPageData(string $pageSlug): HistoricalLocationPageData
    {
        $heroRaw = $this->cmsContentRepository->getSectionContent($pageSlug, SharedSectionKeys::SECTION_HERO);

        if (empty($heroRaw)) {
            throw new HistoricalLocationNotFoundException($pageSlug);
        }
        $rawContent = $this->cmsContentRepository->getPageContent($pageSlug);
        return new HistoricalLocationPageData(
            heroSection: $this->globalContentRepo->mapHeroFromRaw($heroRaw),
            locationHeroSection: HistoricalLocationMapper::mapHero($rawContent['hero_section']),
            introSection: HistoricalLocationMapper::mapIntro($rawContent['intro_section']),
            factsSection: HistoricalLocationMapper::mapFacts($rawContent['facts_section']),
            significanceSection: HistoricalLocationMapper::mapSignificance($rawContent['significance_section']),
            globalUiContent: $this->loadGlobalUi(),
        );
    }

}
