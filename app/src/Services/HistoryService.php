<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoryPageConstants;
use App\Models\HeroSectionContent;
use App\Models\GradientSectionContent;
use App\Models\IntroSectionContent;
use App\Models\HistoryPageData;
use App\Models\HistoryRouteSectionContent;
use App\Models\HistoryTicketOptionsSectionContent;
use App\Models\HistoryTourInfoSectionContent;
use App\Models\HistoryVenuesSectionContent;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IHistoryService;

class HistoryService implements IHistoryService
{
    public function __construct(
        private readonly ICmsPageContentService $cmsService,
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
            heroSection:          HeroSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_HERO)),
            gradientSection:      GradientSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_GRADIENT)),
            introSection:         IntroSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_INTRO)),
            routeSection:         HistoryRouteSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_ROUTE)),
            venuesSection:        HistoryVenuesSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_VENUES)),
            ticketOptionsSection: HistoryTicketOptionsSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_TICKET_OPTIONS)),
            tourInfoSection:      HistoryTourInfoSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, HistoryPageConstants::SECTION_TOUR_INFO)),
            globalUiContent:      $this->globalUiLoader->load(),
        );
    }
}
