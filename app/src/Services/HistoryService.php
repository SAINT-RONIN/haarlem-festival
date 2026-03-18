<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoryPageConstants;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\ICmsPageContentService;

/**
 * Service for preparing history page data.
 *
 * Assembles all data needed for the history view, including
 * event types, locations, and schedule information.
 */
class HistoryService implements IHistoryService
{
    public function __construct(
        private readonly ICmsPageContentService $cmsService,
    ) {
    }

    /**
     * Builds the history page view model with all required data.
     */
    public function getHistoryPageData(): array
    {
        $pageSlug = HistoryPageConstants::PAGE_SLUG;

        return [
            'sections' => [
                HistoryPageConstants::SECTION_HERO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_HERO,
                ),
                HistoryPageConstants::SECTION_GRADIENT => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_GRADIENT,
                ),
                HistoryPageConstants::SECTION_INTRO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_INTRO,
                ),
                HistoryPageConstants::SECTION_ROUTE => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_ROUTE,
                ),
                HistoryPageConstants::SECTION_VENUES => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_VENUES,
                ),
                HistoryPageConstants::SECTION_TICKET_OPTIONS => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_TICKET_OPTIONS,
                ),
                HistoryPageConstants::SECTION_TOUR_INFO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoryPageConstants::SECTION_TOUR_INFO,
                ),
            ],
        ];
    }
}
