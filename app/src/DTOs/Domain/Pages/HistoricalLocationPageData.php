<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\HistoricalLocationFactsContent;
use App\DTOs\Cms\HistoricalLocationHeroContent;
use App\DTOs\Cms\HistoricalLocationIntroContent;
use App\DTOs\Cms\HistoricalLocationSignificanceContent;

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
