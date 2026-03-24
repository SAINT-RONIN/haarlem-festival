<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for the jazz page artists carousel section (section title, artist card labels,
 * carousel settings). Hydrated from CMS key-value pairs.
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
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            artistsHeading: $raw['artists_heading'] ?? null,
            artistsGumboKingsName: $raw['artists_gumbokings_name'] ?? null,
            artistsGumboKingsGenre: $raw['artists_gumbokings_genre'] ?? null,
            artistsGumboKingsDescription: $raw['artists_gumbokings_description'] ?? null,
            artistsGumboKingsImage: $raw['artists_gumbokings_image'] ?? null,
            artistsGumboKingsPerformanceCount: $raw['artists_gumbokings_performance_count'] ?? null,
            artistsGumboKingsFirstPerformance: $raw['artists_gumbokings_first_performance'] ?? null,
            artistsGumboKingsMorePerformancesText: $raw['artists_gumbokings_more_performances_text'] ?? null,
            artistsGumboKingsProfileUrl: $raw['artists_gumbokings_profile_url'] ?? null,
            artistsEvolveName: $raw['artists_evolve_name'] ?? null,
            artistsEvolveGenre: $raw['artists_evolve_genre'] ?? null,
            artistsEvolveDescription: $raw['artists_evolve_description'] ?? null,
            artistsEvolveImage: $raw['artists_evolve_image'] ?? null,
            artistsEvolvePerformanceCount: $raw['artists_evolve_performance_count'] ?? null,
            artistsEvolveFirstPerformance: $raw['artists_evolve_first_performance'] ?? null,
            artistsEvolveMorePerformancesText: $raw['artists_evolve_more_performances_text'] ?? null,
            artistsEvolveProfileUrl: $raw['artists_evolve_profile_url'] ?? null,
            artistsNtjamName: $raw['artists_ntjam_name'] ?? null,
            artistsNtjamGenre: $raw['artists_ntjam_genre'] ?? null,
            artistsNtjamDescription: $raw['artists_ntjam_description'] ?? null,
            artistsNtjamImage: $raw['artists_ntjam_image'] ?? null,
            artistsNtjamPerformanceCount: $raw['artists_ntjam_performance_count'] ?? null,
            artistsNtjamFirstPerformance: $raw['artists_ntjam_first_performance'] ?? null,
            artistsNtjamMorePerformancesText: $raw['artists_ntjam_more_performances_text'] ?? null,
            artistsNtjamProfileUrl: $raw['artists_ntjam_profile_url'] ?? null,
        );
    }
}
