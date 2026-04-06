<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;
use App\DTOs\Domain\Events\JazzArtistCardRecord;
use App\DTOs\Cms\JazzArtistsSectionContent;
use App\DTOs\Cms\JazzBookingCtaSectionContent;
use App\DTOs\Cms\JazzPricingSectionContent;
use App\DTOs\Cms\JazzScheduleCtaSectionContent;
use App\DTOs\Cms\JazzVenuesSectionContent;

/**
 * Carries all CMS sections and domain data needed to render the Jazz overview page.
 */
final readonly class JazzPageData
{
    /**
     * @param PassType[] $passPrices
     * @param JazzArtistCardRecord[] $featuredArtists
     */
    public function __construct(
        public HeroSectionContent $heroSection,
        public GradientSectionContent $gradientSection,
        public IntroSectionContent $introSection,
        public JazzVenuesSectionContent $venuesSection,
        public JazzPricingSectionContent $pricingSection,
        public JazzScheduleCtaSectionContent $scheduleCtaSection,
        public JazzArtistsSectionContent $artistsSection,
        public JazzBookingCtaSectionContent $bookingCtaSection,
        public array $featuredArtists,
        public array $passPrices,
        public GlobalUiContent $globalUiContent,
    ) {}
}
