<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\RestaurantPageConstants;
use App\Models\RestaurantDetailEvent;
use App\Models\RestaurantListingData;
use App\Models\RestaurantPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\RestaurantContentRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant listing page.
 *
 *  - IEventRepository              → lists all active restaurant events
 *  - IGlobalContentRepository      → hero and global UI sections
 *  - IRestaurantContentRepository  → restaurant-specific page CMS sections
 *  - IMediaAssetRepository         → resolves featured image asset IDs to file paths
 */
class RestaurantService implements IRestaurantService
{
    public function __construct(
        private readonly GlobalContentRepository     $globalContentRepo,
        private readonly RestaurantContentRepository $restaurantContentRepo,
        private readonly IEventRepository            $eventRepository,
        private readonly IMediaAssetRepository       $mediaAssetRepository,
    ) {
    }

    public function getRestaurantPageData(?string $cuisineFilter = null): RestaurantPageData
    {
        $events      = $this->eventRepository->findActiveRestaurantEvents();
        $restaurants = array_map(fn(RestaurantDetailEvent $e) => $this->buildListingData($e), $events);

        $slug = RestaurantPageConstants::PAGE_SLUG;

        return new RestaurantPageData(
            heroContent:         $this->globalContentRepo->findHeroContent($slug),
            globalUiContent:     $this->globalContentRepo->findGlobalUiContent(GlobalUiConstants::PAGE_SLUG, GlobalUiConstants::SECTION_KEY),
            gradientSection:     $this->restaurantContentRepo->findGradientContent($slug, RestaurantPageConstants::SECTION_GRADIENT),
            introSplitSection:   $this->restaurantContentRepo->findIntroContent($slug, RestaurantPageConstants::SECTION_INTRO_SPLIT),
            introSplit2Section:  $this->restaurantContentRepo->findIntroSplit2Content($slug, RestaurantPageConstants::SECTION_INTRO_SPLIT2),
            instructionsSection: $this->restaurantContentRepo->findInstructionsContent($slug, RestaurantPageConstants::SECTION_INSTRUCTIONS),
            cardsSection:        $this->restaurantContentRepo->findCardsContent($slug, RestaurantPageConstants::SECTION_CARDS),
            restaurants:         $restaurants,
            activeFilter:        $cuisineFilter,
        );
    }

    private function buildListingData(RestaurantDetailEvent $event): RestaurantListingData
    {
        return new RestaurantListingData(
            event:     $event,
            cms:       $this->restaurantContentRepo->findEventCmsData(
                           RestaurantPageConstants::PAGE_SLUG,
                           'restaurant_event_' . $event->eventId,
                       ),
            imagePath: $this->resolveImagePath($event->featuredImageAssetId),
        );
    }

    private function resolveImagePath(?int $assetId): ?string
    {
        if ($assetId === null) {
            return null;
        }
        return $this->mediaAssetRepository->findById($assetId)?->filePath;
    }
}
