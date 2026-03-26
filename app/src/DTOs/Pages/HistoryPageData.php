<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Models\GlobalUiContent;
use App\Models\GradientSectionContent;
use App\Models\HeroSectionContent;
use App\Models\HistoryRouteSectionContent;
use App\Models\HistoryTicketOptionsSectionContent;
use App\Models\HistoryTourInfoSectionContent;
use App\Models\HistoryVenuesSectionContent;
use App\Models\IntroSectionContent;

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
