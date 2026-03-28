<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoryPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Pages\HistoryPageData;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IHistoryContentRepository;
use App\Services\Interfaces\IHistoryService;

/**
 * Composes the CMS-driven domain payload for the History overview page.
 */
class HistoryService extends BaseContentService implements IHistoryService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private readonly IHistoryContentRepository $historyContentRepo,
    ) {
        parent::__construct($globalContentRepo);
    }

    public function getHistoryPageData(): HistoryPageData
    {
        return $this->guardPageLoad(
            fn (): HistoryPageData => $this->buildPageData(HistoryPageConstants::PAGE_SLUG),
            'Failed to load the History page.',
        );
    }

    private function buildPageData(string $pageSlug): HistoryPageData
    {
        return new HistoryPageData(
            heroSection:          $this->globalContentRepo->findHeroContentBySection($pageSlug, SharedSectionKeys::SECTION_HERO),
            gradientSection:      $this->globalContentRepo->findGradientContent($pageSlug, SharedSectionKeys::SECTION_GRADIENT),
            introSection:         $this->globalContentRepo->findIntroContent($pageSlug, SharedSectionKeys::SECTION_INTRO),
            routeSection:         $this->historyContentRepo->findRouteContent($pageSlug, HistoryPageConstants::SECTION_ROUTE),
            venuesSection:        $this->historyContentRepo->findVenuesContent($pageSlug, HistoryPageConstants::SECTION_VENUES),
            ticketOptionsSection: $this->historyContentRepo->findTicketOptionsContent($pageSlug, HistoryPageConstants::SECTION_TICKET_OPTIONS),
            tourInfoSection:      $this->historyContentRepo->findTourInfoContent($pageSlug, HistoryPageConstants::SECTION_TOUR_INFO),
            globalUiContent:      $this->loadGlobalUi(),
        );
    }
}
