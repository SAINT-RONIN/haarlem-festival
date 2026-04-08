<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzPageConstants;
use App\Constants\SharedSectionKeys;
use App\Enums\EventTypeId;
use App\DTOs\Domain\Pages\JazzPageData;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IJazzContentRepository;
use App\Repositories\Interfaces\IPassTypeRepository;
use App\Services\Interfaces\IJazzService;

/**
 * Service for Jazz page domain payload.
 *
 * This service only composes raw domain/CMS data.
 * ViewModel mapping and UI defaults are handled in the ViewModel layer.
 */
class JazzService extends BaseContentService implements IJazzService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private readonly IJazzContentRepository $jazzContentRepo,
        private readonly IEventRepository $eventRepository,
        private readonly IPassTypeRepository $passTypeRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    /**
     * Returns the complete domain payload for the Jazz overview page,
     * including all CMS sections and Jazz pass prices.
     */
    public function getJazzPageData(): JazzPageData
    {
        return $this->guardPageLoad(
            fn(): JazzPageData => $this->buildPageData(JazzPageConstants::PAGE_SLUG),
            'Failed to load the Jazz page.',
        );
    }

    /**
     * Fetches every CMS section for the Jazz page and combines them
     * with pass-type pricing and shared global-UI content.
     */
    private function buildPageData(string $pageSlug): JazzPageData
    {
        return new JazzPageData(
            heroSection: $this->globalContentRepo->findHeroContent($pageSlug),
            gradientSection: $this->globalContentRepo->findGradientContent($pageSlug, SharedSectionKeys::SECTION_GRADIENT),
            introSection: $this->globalContentRepo->findIntroContent($pageSlug, SharedSectionKeys::SECTION_INTRO),
            venuesSection: $this->jazzContentRepo->findVenuesContent($pageSlug, JazzPageConstants::SECTION_VENUES),
            pricingSection: $this->jazzContentRepo->findPricingContent($pageSlug, JazzPageConstants::SECTION_PRICING),
            scheduleCtaSection: $this->jazzContentRepo->findScheduleCtaContent($pageSlug, JazzPageConstants::SECTION_SCHEDULE_CTA),
            artistsSection: $this->jazzContentRepo->findArtistsContent($pageSlug, JazzPageConstants::SECTION_ARTISTS),
            bookingCtaSection: $this->jazzContentRepo->findBookingCtaContent($pageSlug, JazzPageConstants::SECTION_BOOKING_CTA),
            featuredArtists: $this->eventRepository->findJazzOverviewArtists(),
            passPrices: $this->passTypeRepository->findByEventType(EventTypeId::Jazz->value),
            globalUiContent: $this->loadGlobalUi(),
        );
    }
}
