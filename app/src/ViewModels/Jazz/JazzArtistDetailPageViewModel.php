<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\Schedule\ScheduleEventCardViewModel;

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
    ) {}
}
