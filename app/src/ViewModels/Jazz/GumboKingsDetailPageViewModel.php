<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * ViewModel for the Gumbo Kings detail page.
 */
final readonly class GumboKingsDetailPageViewModel
{
    /**
     * @param array<string> $lineup
     * @param array<string> $highlights
     * @param array<string> $galleryImages
     * @param array<GumboKingsAlbumData> $albums
     * @param array<GumboKingsTrackData> $tracks
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
        public array $lineup,
        public array $highlights,
        public array $galleryImages,
        public array $albums,
        public string $albumsDescription,
        public array $tracks,
        public string $listenHeading,
        public string $listenSubheading,
        public string $listenDescription,
        public string $liveCtaHeading,
        public string $liveCtaDescription,
        public string $performancesHeading,
        public string $performancesDescription,
        public array $performances,
    ) {
    }
}
