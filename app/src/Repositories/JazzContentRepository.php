<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\JazzContentMapper;
use App\DTOs\Cms\JazzArtistsSectionContent;
use App\DTOs\Cms\JazzBookingCtaSectionContent;
use App\DTOs\Cms\JazzPricingSectionContent;
use App\DTOs\Cms\JazzScheduleCtaSectionContent;
use App\DTOs\Cms\JazzVenuesSectionContent;

class JazzContentRepository extends BaseContentRepository implements Interfaces\IJazzContentRepository
{
    public function findVenuesContent(string $pageSlug, string $sectionKey): JazzVenuesSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapVenues($raw);
    }

    public function findPricingContent(string $pageSlug, string $sectionKey): JazzPricingSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapPricing($raw);
    }

    public function findScheduleCtaContent(string $pageSlug, string $sectionKey): JazzScheduleCtaSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapScheduleCta($raw);
    }

    public function findArtistsContent(string $pageSlug, string $sectionKey): JazzArtistsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapArtists($raw);
    }

    public function findBookingCtaContent(string $pageSlug, string $sectionKey): JazzBookingCtaSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return JazzContentMapper::mapBookingCta($raw);
    }
}
