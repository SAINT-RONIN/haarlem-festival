<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Content\JazzArtistsSectionContent;
use App\Content\JazzBookingCtaSectionContent;
use App\Content\JazzPricingSectionContent;
use App\Content\JazzScheduleCtaSectionContent;
use App\Content\JazzVenuesSectionContent;

/**
 * Typed access to Jazz page CMS content sections.
 */
interface IJazzContentRepository
{
    /** Fetches the jazz venues section content. */
    public function findVenuesContent(string $pageSlug, string $sectionKey): JazzVenuesSectionContent;

    /** Fetches the jazz pricing section content. */
    public function findPricingContent(string $pageSlug, string $sectionKey): JazzPricingSectionContent;

    /** Fetches the jazz schedule CTA section content. */
    public function findScheduleCtaContent(string $pageSlug, string $sectionKey): JazzScheduleCtaSectionContent;

    /** Fetches the jazz artists section content. */
    public function findArtistsContent(string $pageSlug, string $sectionKey): JazzArtistsSectionContent;

    /** Fetches the jazz booking CTA section content. */
    public function findBookingCtaContent(string $pageSlug, string $sectionKey): JazzBookingCtaSectionContent;
}
