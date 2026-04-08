<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoricalLocationPageConstants;
use App\Constants\SharedSectionKeys;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\DTOs\Domain\Pages\HistoricalLocationPageData;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IHistoricalLocationContentRepository;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IHistoricalLocationService;

/**
 * Composes the CMS-driven domain payload for a single historical location page.
 */
class HistoricalLocationService extends BaseContentService implements IHistoricalLocationService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContentRepository,
        IGlobalContentRepository $globalContentRepo,
        private readonly IHistoricalLocationContentRepository $histLocContentRepo,
    ) {
        parent::__construct($globalContentRepo);
    }

    /** Loads the full CMS-driven payload for one historical location page by slug. */
    public function getHistoralLocationPageData(string $name): HistoricalLocationPageData
    {
        $heroRaw = $this->cmsContentRepository->getSectionContent($name, SharedSectionKeys::SECTION_HERO);

        if (empty($heroRaw)) {
            throw new HistoricalLocationNotFoundException($name);
        }

        return $this->buildPageData($name, $heroRaw);
    }

    /** Builds the final page data object once the required hero content has been found. */
    /** @param array<string, ?string> $heroRaw */
    private function buildPageData(string $slug, array $heroRaw): HistoricalLocationPageData
    {
        return new HistoricalLocationPageData(
            heroSection: $this->globalContentRepo->mapHeroFromRaw($heroRaw),
            locationHeroSection: $this->histLocContentRepo->mapHeroFromRaw($heroRaw),
            introSection: $this->histLocContentRepo->findIntroContent($slug, SharedSectionKeys::SECTION_INTRO),
            factsSection: $this->histLocContentRepo->findFactsContent($slug, HistoricalLocationPageConstants::SECTION_FACTS),
            significanceSection: $this->histLocContentRepo->findSignificanceContent($slug, HistoricalLocationPageConstants::SECTION_SIGNIFICANCE),
            globalUiContent: $this->loadGlobalUi(),
        );
    }
}
