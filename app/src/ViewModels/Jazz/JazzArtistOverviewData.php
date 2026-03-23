<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

final readonly class JazzArtistOverviewData
{
    public function __construct(
        public string $overviewHeading,
        public string $overviewLead,
        public string $overviewBodyPrimary,
        public string $overviewBodySecondary,
    ) {}
}
