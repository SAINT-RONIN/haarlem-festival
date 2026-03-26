<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Jazz artists_section.
 */
final readonly class JazzArtistsSectionContent
{
    public function __construct(
        public ?string $artistsHeading,
        public ?string $artistsGumboKingsName,
        public ?string $artistsGumboKingsGenre,
        public ?string $artistsGumboKingsDescription,
        public ?string $artistsGumboKingsImage,
        public ?string $artistsGumboKingsPerformanceCount,
        public ?string $artistsGumboKingsFirstPerformance,
        public ?string $artistsGumboKingsMorePerformancesText,
        public ?string $artistsGumboKingsProfileUrl,
        public ?string $artistsEvolveName,
        public ?string $artistsEvolveGenre,
        public ?string $artistsEvolveDescription,
        public ?string $artistsEvolveImage,
        public ?string $artistsEvolvePerformanceCount,
        public ?string $artistsEvolveFirstPerformance,
        public ?string $artistsEvolveMorePerformancesText,
        public ?string $artistsEvolveProfileUrl,
        public ?string $artistsNtjamName,
        public ?string $artistsNtjamGenre,
        public ?string $artistsNtjamDescription,
        public ?string $artistsNtjamImage,
        public ?string $artistsNtjamPerformanceCount,
        public ?string $artistsNtjamFirstPerformance,
        public ?string $artistsNtjamMorePerformancesText,
        public ?string $artistsNtjamProfileUrl,
    ) {
    }
}
