<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Models\GlobalUiContent;
use App\Models\GradientSectionContent;
use App\Models\HeroSectionContent;
use App\Models\IntroSectionContent;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\JazzPricingSectionContent;
use App\Models\JazzScheduleCtaSectionContent;
use App\Models\JazzVenuesSectionContent;

/**
 * Carries all CMS sections and domain data needed to render the Jazz overview page.
 */
final readonly class JazzPageData
{
    /**
     * @param PassType[] $passPrices
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
        public array $passPrices,
        public GlobalUiContent $globalUiContent,
    ) {}
}
