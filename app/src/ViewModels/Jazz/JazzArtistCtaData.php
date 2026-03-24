<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * CTA section for a jazz artist detail page — button text and link.
 */
final readonly class JazzArtistCtaData
{
    public function __construct(
        public string $liveCtaHeading,
        public string $liveCtaDescription,
        public string $liveCtaBookButtonText,
        public string $liveCtaScheduleButtonText,
        public string $liveCtaScheduleButtonUrl,
        public string $performancesSectionId,
        public string $performancesHeading,
        public string $performancesDescription,
    ) {}
}
