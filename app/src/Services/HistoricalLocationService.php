<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoricalLocationPageConstants;
use App\Exceptions\HistoricalLocationNotFoundException;
use App\DTOs\Pages\HistoricalLocationPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\HistoricalLocationContentRepository;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IHistoricalLocationService;

/**
 * Composes the CMS-driven domain payload for a single historical location page.
 */
class HistoricalLocationService extends BaseContentService implements IHistoricalLocationService
{
    public function __construct(
        ICmsContentRepository $cmsContentRepository,
        private readonly GlobalContentRepository $globalContentRepo,
        private readonly HistoricalLocationContentRepository $histLocContentRepo,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
        parent::__construct($cmsContentRepository);
    }

    public function getHistoralLocationPageData(string $name): HistoricalLocationPageData
    {
        $heroRaw = $this->cmsContentRepository->getSectionContent($name, HistoricalLocationPageConstants::SECTION_HERO);

        if (empty($heroRaw)) {
            throw new HistoricalLocationNotFoundException($name);
        }

        return $this->buildPageData($name, $heroRaw);
    }

    /** @param array<string, ?string> $heroRaw */
    private function buildPageData(string $slug, array $heroRaw): HistoricalLocationPageData
    {
        return new HistoricalLocationPageData(
            heroSection:         $this->globalContentRepo->mapHeroFromRaw($heroRaw),
            locationHeroSection: $this->histLocContentRepo->mapHeroFromRaw($heroRaw),
            introSection:        $this->histLocContentRepo->findIntroContent($slug, HistoricalLocationPageConstants::SECTION_INTRO),
            factsSection:        $this->histLocContentRepo->findFactsContent($slug, HistoricalLocationPageConstants::SECTION_FACTS),
            significanceSection: $this->histLocContentRepo->findSignificanceContent($slug, HistoricalLocationPageConstants::SECTION_SIGNIFICANCE),
            globalUiContent:     $this->globalUiLoader->load(),
        );
    }
}
