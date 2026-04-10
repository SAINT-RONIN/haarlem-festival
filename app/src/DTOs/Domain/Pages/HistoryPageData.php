<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\HistoryRouteSectionContent;
use App\DTOs\Cms\HistoryTicketOptionsSectionContent;
use App\DTOs\Cms\HistoryTourInfoSectionContent;
use App\DTOs\Cms\HistoryVenuesSectionContent;
use App\DTOs\Cms\IntroSectionContent;

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
