<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Overview section data for a jazz artist detail page — biography text and pull quote.
 */
final readonly class JazzArtistOverviewData
{
    public function __construct(
        public string $overviewHeading,
        public string $overviewLead,
        public string $overviewBodyPrimary,
        public string $overviewBodySecondary,
    ) {}
}
