<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantDetailConstants;
use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Pages\RestaurantListingData;
use App\DTOs\Pages\RestaurantPageData;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IRestaurantContentRepository;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant listing page.
 *
 * Fetches event-based restaurant listings and CMS content sections,
 * then returns a typed RestaurantPageData for the mapper.
 */
class RestaurantService extends BaseContentService implements IRestaurantService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private readonly IRestaurantContentRepository $restaurantContentRepo,
        private readonly IRestaurantRepository $restaurantRepository,
        private readonly IEventRepository $eventRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    /** Loads all CMS sections and restaurant listings needed by the restaurant overview page. */
    public function getRestaurantPageData(): RestaurantPageData
    {
        return $this->guardPageLoad(
            fn (): RestaurantPageData => $this->assembleRestaurantPageData(),
            'Failed to load the Restaurant page.',
        );
    }

    /** Builds the restaurant page payload from CMS content and restaurant event data. */
    private function assembleRestaurantPageData(): RestaurantPageData
    {
        return new RestaurantPageData(
            heroContent: $this->globalContentRepo->findHeroContent(RestaurantPageConstants::PAGE_SLUG),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: $this->globalContentRepo->findGradientContent(RestaurantPageConstants::PAGE_SLUG, SharedSectionKeys::SECTION_GRADIENT),
            introSplitSection: $this->restaurantContentRepo->findIntroContent(RestaurantPageConstants::PAGE_SLUG, SharedSectionKeys::SECTION_INTRO_SPLIT),
            introSplit2Section: $this->restaurantContentRepo->findIntroSplit2Content(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INTRO_SPLIT2),
            instructionsSection: $this->restaurantContentRepo->findInstructionsContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INSTRUCTIONS),
            cardsSection: $this->restaurantContentRepo->findCardsContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_CARDS),
            listings: $this->buildEventListings(),
        );
    }

    /**
     * Converts active restaurant events into listing cards enriched with CMS content and images.
     *
     * @return RestaurantListingData[]
     */
    private function buildEventListings(): array
    {
        $events = $this->eventRepository->findActiveRestaurantEvents();
        $restaurantsById = [];

        foreach ($this->restaurantRepository->findAllActive() as $restaurant) {
            $restaurantsById[$restaurant->restaurantId] = $restaurant;
        }

        $listings = [];

        foreach ($events as $event) {
            // Each event card mixes event data, CMS content, image data, and optional restaurant details.
            $cms = $this->restaurantContentRepo->findEventCmsData(
                RestaurantDetailConstants::PAGE_SLUG,
                RestaurantDetailConstants::eventSectionKey($event->eventId),
            );

            $imagePath = $event->featuredImageAssetId !== null
                ? $this->mediaAssetRepository->findById($event->featuredImageAssetId)?->filePath
                : null;

            $listings[] = new RestaurantListingData(
                event: $event,
                cms: $cms,
                imagePath: $imagePath,
                restaurant: $restaurantsById[$event->restaurantId] ?? null,
            );
        }

        return $listings;
    }
}
