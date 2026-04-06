<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Content\GlobalUiContent;
use App\Content\GradientSectionContent;
use App\Content\HeroSectionContent;
use App\Content\HistoryRouteSectionContent;
use App\Content\HistoryTicketOptionsSectionContent;
use App\Content\HistoryTourInfoSectionContent;
use App\Content\HistoryVenuesSectionContent;
use App\Content\IntroSectionContent;

/**
 * Carries all CMS sections needed to render the History overview page.
 * Returned by HistoryService and consumed by HistoryMapper.
 */
final readonly class HistoryPageData
{
    public function __construct(
        public HeroSectionContent $heroSection,
        public GradientSectionContent $gradientSection,
        public IntroSectionContent $introSection,
        public HistoryRouteSectionContent $routeSection,
        public HistoryVenuesSectionContent $venuesSection,
        public HistoryTicketOptionsSectionContent $ticketOptionsSection,
        public HistoryTourInfoSectionContent $tourInfoSection,
        public GlobalUiContent $globalUiContent,
    ) {}
}
