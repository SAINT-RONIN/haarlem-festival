<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Models\HistoricalLocationFactsContent;
use App\Models\HistoricalLocationHeroContent;
use App\Models\HistoricalLocationIntroContent;
use App\Models\HistoricalLocationSignificanceContent;

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
