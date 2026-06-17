<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\HistoricalLocationFactsContent;
use App\DTOs\Cms\HistoricalLocationHeroContent;
use App\DTOs\Cms\HistoricalLocationIntroContent;
use App\DTOs\Cms\HistoricalLocationSignificanceContent;

//carries all CMS sections needed to render a HistoricalLocation detail page
final readonly class HistoricalLocationPageData
{
    public function __construct(
        public HistoricalLocationHeroContent $locationHeroSection,
        public HistoricalLocationIntroContent $introSection,
        public HistoricalLocationFactsContent $factsSection,
        public HistoricalLocationSignificanceContent $significanceSection,
        public GlobalUiContent $globalUiContent,
    ) {}
}
