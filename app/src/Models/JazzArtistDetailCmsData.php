<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for a single Jazz artist detail page.
 */
final readonly class JazzArtistDetailCmsData
{
    public function __construct(
        public ?string $heroSubtitle,
        public ?string $heroBackgroundImage,
        public ?string $originText,
        public ?string $formedText,
        public ?string $performancesText,
        public ?string $heroBackButtonText,
        public ?string $heroBackButtonUrl,
        public ?string $heroReserveButtonText,
        public ?string $overviewHeading,
        public ?string $overviewLead,
        public ?string $overviewBodyPrimary,
        public ?string $overviewBodySecondary,
        public ?string $lineupHeading,
        public ?string $highlightsHeading,
        public ?string $photoGalleryHeading,
        public ?string $photoGalleryDescription,
        public ?string $albumsHeading,
        public ?string $albumsDescription,
        public ?string $listenHeading,
        public ?string $listenSubheading,
        public ?string $listenDescription,
        public ?string $listenPlayButtonLabel,
        public ?string $listenPlayExcerptText,
        public ?string $listenTrackArtworkAltSuffix,
        public ?string $liveCtaHeading,
        public ?string $liveCtaDescription,
        public ?string $liveCtaBookButtonText,
        public ?string $liveCtaScheduleButtonText,
        public ?string $liveCtaScheduleButtonUrl,
        public ?string $performancesSectionId,
        public ?string $performancesHeading,
        public ?string $performancesDescription,
    ) {
    }
}
