<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * View data for a single jazz artist detail page.
 *
 * Carries hero, overview, lineup, media, albums, and upcoming sessions.
 */
final readonly class JazzArtistDetailPageViewModel
{
    /**
     * @param ScheduleEventCardViewModel[] $performances
     */
    public function __construct(
        public JazzArtistHeroData $hero,
        public JazzArtistOverviewData $overview,
        public JazzArtistLineupData $lineup,
        public JazzArtistMediaData $media,
        public JazzArtistCtaData $cta,
        public array $performances,
        public string $shareUrl,
    ) {}
}
