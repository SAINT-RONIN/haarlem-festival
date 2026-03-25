<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzPageConstants;
use App\Enums\EventTypeId;
use App\Models\HeroSectionContent;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\GradientSectionContent;
use App\Models\IntroSectionContent;
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
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    /**
     * Returns the complete domain payload for the Jazz overview page,
     * including all CMS sections and Jazz pass prices.
     */
    public function getJazzPageData(): JazzPageData
    {
        return $this->buildPageData(JazzPageConstants::PAGE_SLUG);
    }

    /**
     * Fetches every CMS section for the Jazz page and combines them
     * with pass-type pricing and shared global-UI content.
     */
    private function buildPageData(string $pageSlug): JazzPageData
    {
        return new JazzPageData(
            heroSection:        HeroSectionContent::fromRawArray($this->cmsService->getHeroSectionContent($pageSlug)),
            gradientSection:    GradientSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_GRADIENT)),
            introSection:       IntroSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_INTRO)),
            venuesSection:      JazzVenuesSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_VENUES)),
            pricingSection:     JazzPricingSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_PRICING)),
            scheduleCtaSection: JazzScheduleCtaSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_SCHEDULE_CTA)),
            artistsSection:     JazzArtistsSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_ARTISTS)),
            bookingCtaSection: JazzBookingCtaSectionContent::fromRawArray($this->cmsService->getSectionContent($pageSlug, JazzPageConstants::SECTION_BOOKING_CTA)),
            passPrices: $this->passTypeRepository->findByEventType(EventTypeId::Jazz->value),
            globalUiContent: $this->globalUiLoader->load(),
        );
    }
}
