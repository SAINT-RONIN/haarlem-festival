<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoricalLocationPageConstants;
use App\Constants\HistoryPageConstants;
use App\Services\Interfaces\IHistoricalLocationService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\ICmsPageContentService;

/**
 * Service for preparing history page data.
 *
 * Assembles all data needed for the history view, including
 * event types, locations, and schedule information.
 */
class HistoricalLocationService implements IHistoricalLocationService
{
    public function __construct(
        private readonly ICmsPageContentService $cmsService,
    ) {
    }

    /**
     * Builds the historical location page view model with all required data.
     */
    public function getHistoralLocationPageData(string $name): array
    {
        $pageSlug = $name;

        return [
            'sections' => [
                HistoricalLocationPageConstants::SECTION_HERO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoricalLocationPageConstants::SECTION_HERO,
                ),
                HistoricalLocationPageConstants::SECTION_INTRO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoricalLocationPageConstants::SECTION_INTRO,
                ),
                HistoricalLocationPageConstants::SECTION_FACTS => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoricalLocationPageConstants::SECTION_FACTS,
                ),
                HistoricalLocationPageConstants::SECTION_SIGNIFICANCE => $this->cmsService->getSectionContent(
                    $pageSlug,
                    HistoricalLocationPageConstants::SECTION_SIGNIFICANCE
                ),
                'global_ui' => $this->cmsService->getSectionContent('home', 'global_ui'),
            ],
        ];
    }
}
