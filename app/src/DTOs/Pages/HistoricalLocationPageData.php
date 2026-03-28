<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Content\GlobalUiContent;
use App\Content\HeroSectionContent;
use App\Content\HistoricalLocationFactsContent;
use App\Content\HistoricalLocationHeroContent;
use App\Content\HistoricalLocationIntroContent;
use App\Content\HistoricalLocationSignificanceContent;

/**
 * Carries all CMS sections needed to render a HistoricalLocation detail page.
 * Returned by HistoricalLocationService and consumed by HistoricalLocationMapper.
 */
final readonly class HistoricalLocationPageData
{
    public function __construct(
        public HeroSectionContent $heroSection,
        public HistoricalLocationHeroContent $locationHeroSection,
        public HistoricalLocationIntroContent $introSection,
        public HistoricalLocationFactsContent $factsSection,
        public HistoricalLocationSignificanceContent $significanceSection,
        public GlobalUiContent $globalUiContent,
    ) {}
}
