<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries all CMS sections needed to render the History overview page.
 * Returned by HistoryService and consumed by HistoryMapper.
 */
final readonly class HistoryPageData
{
    public function __construct(
        public HeroSectionContent $heroSection,
        public HistoryGradientSectionContent $gradientSection,
        public HistoryIntroSectionContent $introSection,
        public HistoryRouteSectionContent $routeSection,
        public HistoryVenuesSectionContent $venuesSection,
        public HistoryTicketOptionsSectionContent $ticketOptionsSection,
        public HistoryTourInfoSectionContent $tourInfoSection,
        public GlobalUiContent $globalUiContent,
    ) {}
}
