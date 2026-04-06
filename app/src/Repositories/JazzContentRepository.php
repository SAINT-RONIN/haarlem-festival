<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\JazzContentMapper;
use App\DTOs\Cms\JazzArtistsSectionContent;
use App\DTOs\Cms\JazzBookingCtaSectionContent;
use App\DTOs\Cms\JazzPricingSectionContent;
use App\DTOs\Cms\JazzScheduleCtaSectionContent;
use App\DTOs\Cms\JazzVenuesSectionContent;

/**
 * Provides typed access to Jazz page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to JazzContentMapper.
 */
class JazzContentRepository extends BaseContentRepository implements Interfaces\IJazzContentRepository
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
}
