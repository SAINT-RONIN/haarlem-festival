<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\HistoryContentMapper;
use App\DTOs\Cms\HistoryRouteSectionContent;
use App\DTOs\Cms\HistoryTicketOptionsSectionContent;
use App\DTOs\Cms\HistoryTourInfoSectionContent;
use App\DTOs\Cms\HistoryVenuesSectionContent;

class HistoryContentRepository extends BaseContentRepository implements Interfaces\IHistoryContentRepository
{
    public function findRouteContent(string $pageSlug, string $sectionKey): HistoryRouteSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapRoute($raw);
    }

    public function findVenuesContent(string $pageSlug, string $sectionKey): HistoryVenuesSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapVenues($raw);
    }

    public function findTicketOptionsContent(string $pageSlug, string $sectionKey): HistoryTicketOptionsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapTicketOptions($raw);
    }

    public function findTourInfoContent(string $pageSlug, string $sectionKey): HistoryTourInfoSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapTourInfo($raw);
    }
}
