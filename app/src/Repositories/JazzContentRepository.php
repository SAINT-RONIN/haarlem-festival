<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\JazzContentMapper;
use App\Models\JazzArtistDetailCmsData;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\JazzPricingSectionContent;
use App\Models\JazzScheduleCtaSectionContent;
use App\Models\JazzVenuesSectionContent;

/**
 * Provides typed access to Jazz page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to JazzContentMapper.
 */
class JazzContentRepository extends BaseContentRepository
{
    /** Fetches the jazz venues section content. */
    public function findVenuesContent(string $pageSlug, string $sectionKey): JazzVenuesSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapVenues($raw);
    }

    /** Fetches the jazz pricing section content. */
    public function findPricingContent(string $pageSlug, string $sectionKey): JazzPricingSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapPricing($raw);
    }

    /** Fetches the jazz schedule CTA section content. */
    public function findScheduleCtaContent(string $pageSlug, string $sectionKey): JazzScheduleCtaSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapScheduleCta($raw);
    }

    /** Fetches the jazz artists section content. */
    public function findArtistsContent(string $pageSlug, string $sectionKey): JazzArtistsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapArtists($raw);
    }

    /** Fetches the jazz booking CTA section content. */
    public function findBookingCtaContent(string $pageSlug, string $sectionKey): JazzBookingCtaSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapBookingCta($raw);
    }

    /** Fetches the artist detail CMS data for a specific event section. */
    public function findArtistDetailCmsData(string $pageSlug, string $sectionKey): JazzArtistDetailCmsData
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapArtistDetail($raw);
    }
}
