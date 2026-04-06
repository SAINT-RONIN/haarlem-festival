<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Content\HistoryRouteSectionContent;
use App\Content\HistoryTicketOptionsSectionContent;
use App\Content\HistoryTourInfoSectionContent;
use App\Content\HistoryVenuesSectionContent;

/**
 * Typed access to History page CMS content sections.
 */
interface IHistoryContentRepository
{
    /** Fetches the history route section content. */
    public function findRouteContent(string $pageSlug, string $sectionKey): HistoryRouteSectionContent;

    /** Fetches the history venues section content. */
    public function findVenuesContent(string $pageSlug, string $sectionKey): HistoryVenuesSectionContent;

    /** Fetches the history ticket options section content. */
    public function findTicketOptionsContent(string $pageSlug, string $sectionKey): HistoryTicketOptionsSectionContent;

    /** Fetches the history tour info section content. */
    public function findTourInfoContent(string $pageSlug, string $sectionKey): HistoryTourInfoSectionContent;
}
