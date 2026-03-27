<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\JazzArtistDetailCmsData;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\JazzPricingSectionContent;
use App\Models\JazzScheduleCtaSectionContent;
use App\Models\JazzVenuesSectionContent;

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

    /** Fetches the artist detail CMS data for a specific event section. */
    public function findArtistDetailCmsData(string $pageSlug, string $sectionKey): JazzArtistDetailCmsData;
}
