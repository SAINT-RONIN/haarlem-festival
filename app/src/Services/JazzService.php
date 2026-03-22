<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzPageConstants;
use App\Enums\EventTypeId;
use App\Models\HeroSectionContent;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\JazzGradientSectionContent;
use App\Models\JazzIntroSectionContent;
use App\Models\JazzPageData;
use App\Models\JazzPricingSectionContent;
use App\Models\JazzScheduleCtaSectionContent;
use App\Models\JazzVenuesSectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IPassTypeRepository;
use App\Services\Interfaces\IJazzService;

/**
 * Service for Jazz page domain payload.
 *
 * This service only composes raw domain/CMS data.
 * ViewModel mapping and UI defaults are handled in the ViewModel layer.
 */
class JazzService implements IJazzService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
        private readonly IPassTypeRepository $passTypeRepository,
    ) {
    }

    public function getJazzPageData(): JazzPageData
    {
        $pageSlug = JazzPageConstants::PAGE_SLUG;

        return new JazzPageData(
            heroSection: HeroSectionContent::fromRawArray(
                $this->cmsService->getHeroSectionContent($pageSlug),
            ),
            gradientSection: JazzGradientSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_GRADIENT),
            ),
            introSection: JazzIntroSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_INTRO),
            ),
            venuesSection: JazzVenuesSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_VENUES),
            ),
            pricingSection: JazzPricingSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_PRICING),
            ),
            scheduleCtaSection: JazzScheduleCtaSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_SCHEDULE_CTA),
            ),
            artistsSection: JazzArtistsSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_ARTISTS),
            ),
            bookingCtaSection: JazzBookingCtaSectionContent::fromRawArray(
                $this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_BOOKING_CTA),
            ),
            passPrices: $this->passTypeRepository->findByEventType(EventTypeId::Jazz->value),
        );
    }
}
