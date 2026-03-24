<?php

declare(strict_types=1);

namespace App\Models;

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
