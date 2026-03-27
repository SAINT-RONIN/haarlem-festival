<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\HistoryContentMapper;
use App\Models\HistoryRouteSectionContent;
use App\Models\HistoryTicketOptionsSectionContent;
use App\Models\HistoryTourInfoSectionContent;
use App\Models\HistoryVenuesSectionContent;

/**
 * Provides typed access to History page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to HistoryContentMapper.
 */
class HistoryContentRepository extends BaseContentRepository
{
    /** Fetches the history route section content. */
    public function findRouteContent(string $pageSlug, string $sectionKey): HistoryRouteSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapRoute($raw);
    }

    /** Fetches the history venues section content. */
    public function findVenuesContent(string $pageSlug, string $sectionKey): HistoryVenuesSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapVenues($raw);
    }

    /** Fetches the history ticket options section content. */
    public function findTicketOptionsContent(string $pageSlug, string $sectionKey): HistoryTicketOptionsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapTicketOptions($raw);
    }

    /** Fetches the history tour info section content. */
    public function findTourInfoContent(string $pageSlug, string $sectionKey): HistoryTourInfoSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return HistoryContentMapper::mapTourInfo($raw);
    }
}
