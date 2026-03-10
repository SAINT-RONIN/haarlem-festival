<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * ViewModel for a Jazz artist detail page.
 */
final readonly class JazzArtistDetailPageViewModel
{
    /**
     * @param array<string> $lineup
     * @param array<string> $highlights
     * @param array<string> $galleryImages
     * @param array<JazzArtistAlbumData> $albums
     * @param array<JazzArtistTrackData> $tracks
     * @param array<ScheduleEventCardViewModel> $performances
     */
    public function __construct(
        public string $heroTitle,
        public string $heroSubtitle,
        public string $heroBackgroundImageUrl,
        public string $originText,
        public string $formedText,
        public string $performancesText,
        public string $overviewHeading,
        public string $overviewLead,
        public string $overviewBodyPrimary,
        public string $overviewBodySecondary,
        public string $lineupHeading,
        public array $lineup,
        public string $highlightsHeading,
        public array $highlights,
        public string $photoGalleryHeading,
        public string $photoGalleryDescription,
        public array $galleryImages,
        public string $albumsHeading,
        public string $albumsDescription,
        public array $albums,
        public string $listenHeading,
        public string $listenSubheading,
        public string $listenDescription,
        public array $tracks,
        public string $liveCtaHeading,
        public string $liveCtaDescription,
        public string $performancesSectionId,
        public string $performancesHeading,
        public string $performancesDescription,
        public array $performances,
    ) {
    }
}
