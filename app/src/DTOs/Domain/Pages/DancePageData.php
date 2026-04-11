<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\DanceArtistsSectionContent;
use App\DTOs\Cms\DanceHeadlinersSectionContent;
use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;
use App\DTOs\Domain\Events\JazzArtistCardRecord;

/**
 * Carries all CMS sections and domain data needed to render the Dance overview page.
 */
final readonly class DancePageData
{
    /**
     * @param JazzArtistCardRecord[] $danceArtists All dance artists (headliners + supporting combined).
     */
    public function __construct(
        public HeroSectionContent $heroSection,
        public GradientSectionContent $gradientSection,
        public IntroSectionContent $introSection,
        public DanceHeadlinersSectionContent $headlinersSection,
        public DanceArtistsSectionContent $artistsSection,
        public array $danceArtists,
        public GlobalUiContent $globalUiContent,
    ) {}
}
