<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Pages\HistoryPageData;
use App\Mappers\GlobalContentMapper;
use App\Mappers\HistoryContentMapper;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Services\Interfaces\IHistoryService;
use App\Repositories\Interfaces\ICmsContentRepository;

class HistoryService extends BaseContentService implements IHistoryService
{
    private ICmsContentRepository $cmsContent;
    public function __construct(
        ICmsContentRepository $cmsContent,
        IGlobalContentRepository $globalContentRepo,
    ) {
        parent::__construct($globalContentRepo);
        $this->cmsContent = $cmsContent;
    }

    public function getHistoryPageData(): HistoryPageData
    {
        return $this->guardPageLoad(
            fn(): HistoryPageData => $this->buildPageData('history'),
            'Failed to load the History page.',
        );
    }

    private function buildPageData(string $pageSlug): HistoryPageData
    {
        $rawContent = $this->cmsContent->getPageContent($pageSlug);
        return new HistoryPageData(
            heroSection: GlobalContentMapper::mapHero($rawContent['hero_section']),
            gradientSection: GlobalContentMapper::mapGradient($rawContent['gradient_section']),
            introSection: GlobalContentMapper::mapIntro($rawContent['intro_section']),
            routeSection: HistoryContentMapper::mapRoute($rawContent['route_section']),
            venuesSection: HistoryContentMapper::mapVenues($rawContent['historical_locations_section']),
            ticketOptionsSection: HistoryContentMapper::mapTicketOptions($rawContent['ticket_options_section']),
            tourInfoSection: HistoryContentMapper::mapTourInfo($rawContent['history_important_tour_info_section']),
            globalUiContent: $this->loadGlobalUi(),
        );
    }
}
