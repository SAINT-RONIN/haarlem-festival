<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoryPageConstants;
use App\Models\HistoryPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\HistoryContentRepository;
use App\Services\Interfaces\IHistoryService;

/**
 * Composes the CMS-driven domain payload for the History overview page.
 */
class HistoryService implements IHistoryService
{
    public function __construct(
        private readonly GlobalContentRepository $globalContentRepo,
        private readonly HistoryContentRepository $historyContentRepo,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    public function getHistoryPageData(): HistoryPageData
    {
        return $this->buildPageData(HistoryPageConstants::PAGE_SLUG);
    }

    private function buildPageData(string $pageSlug): HistoryPageData
    {
        return new HistoryPageData(
            heroSection:          $this->globalContentRepo->findHeroContentBySection($pageSlug, HistoryPageConstants::SECTION_HERO),
            gradientSection:      $this->globalContentRepo->findGradientContent($pageSlug, HistoryPageConstants::SECTION_GRADIENT),
            introSection:         $this->globalContentRepo->findIntroContent($pageSlug, HistoryPageConstants::SECTION_INTRO),
            routeSection:         $this->historyContentRepo->findRouteContent($pageSlug, HistoryPageConstants::SECTION_ROUTE),
            venuesSection:        $this->historyContentRepo->findVenuesContent($pageSlug, HistoryPageConstants::SECTION_VENUES),
            ticketOptionsSection: $this->historyContentRepo->findTicketOptionsContent($pageSlug, HistoryPageConstants::SECTION_TICKET_OPTIONS),
            tourInfoSection:      $this->historyContentRepo->findTourInfoContent($pageSlug, HistoryPageConstants::SECTION_TOUR_INFO),
            globalUiContent:      $this->globalUiLoader->load(),
        );
    }
}
