<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Pages\RestaurantDetailData;
use App\DTOs\Pages\RestaurantPageData;
use App\Repositories\Interfaces\ICuisineTypeRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IRestaurantContentRepository;
use App\Repositories\Interfaces\IRestaurantImageRepository;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant page.
 *
 * Returns typed data models.
 * Mapping to ViewModels happens in RestaurantViewMapper.
 *
 * Uses TWO different data sources:
 *  1. CMS (CmsItem table) — for page copy: titles, descriptions, images
 *  2. Domain (Restaurant table) — for real restaurant business data
 */
class RestaurantService extends BaseContentService implements IRestaurantService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private readonly IRestaurantContentRepository $restaurantContentRepo,
        private readonly IRestaurantRepository $restaurantRepository,
        private readonly IRestaurantImageRepository $restaurantImageRepository,
        private readonly ICuisineTypeRepository $cuisineTypeRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
        return $this->guardPageLoad(
            fn (): RestaurantPageData => $this->assembleRestaurantPageData(),
            'Failed to load the Restaurant page.',
        );
    }

    /**
     * Returns detail data for a single restaurant, or null if not found.
     * Uses defensive null-check (foreseeable: restaurant may not exist).
     */
    public function getRestaurantDetailData(int $id): ?RestaurantDetailData
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        return $this->guardPageLoad(
            fn (): RestaurantDetailData => $this->assembleDetailData($restaurant),
            'Failed to load restaurant detail page.',
        );
    }

    /** Fetches all data sources and assembles the restaurant overview page payload. */
    private function assembleRestaurantPageData(): RestaurantPageData
    {
        $restaurants = $this->restaurantRepository->findAllActive();
        $cuisinesByRestaurant = $this->buildCuisineMap($restaurants);

        return new RestaurantPageData(
            heroContent: $this->globalContentRepo->findHeroContent(RestaurantPageConstants::PAGE_SLUG),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: $this->globalContentRepo->findGradientContent(RestaurantPageConstants::PAGE_SLUG, SharedSectionKeys::SECTION_GRADIENT),
            introSplitSection: $this->restaurantContentRepo->findIntroContent(RestaurantPageConstants::PAGE_SLUG, SharedSectionKeys::SECTION_INTRO_SPLIT),
            introSplit2Section: $this->restaurantContentRepo->findIntroSplit2Content(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INTRO_SPLIT2),
            instructionsSection: $this->restaurantContentRepo->findInstructionsContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INSTRUCTIONS),
            cardsSection: $this->restaurantContentRepo->findCardsContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_CARDS),
            restaurants: $restaurants,
            cuisinesByRestaurant: $cuisinesByRestaurant,
        );
    }

    /** Assembles the full detail payload for a single restaurant. */
    private function assembleDetailData(\App\Models\Restaurant $restaurant): RestaurantDetailData
    {
        $images = $this->restaurantImageRepository->findByRestaurantId($restaurant->restaurantId);
        $scheduleData = $this->getRestaurantScheduleData($restaurant->name);
        $cuisineTypes = $this->cuisineTypeRepository->findByRestaurantId($restaurant->restaurantId);

        return new RestaurantDetailData(
            restaurant: $restaurant,
            imagesByType: $this->groupImagesByType($images),
            cms: $this->restaurantContentRepo->findDetailContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_DETAIL),
            globalUiContent: $this->loadGlobalUi(),
            timeSlots: $scheduleData['timeSlots'],
            priceCards: $scheduleData['priceCards'],
            cuisineTypes: $cuisineTypes,
        );
    }

    /**
     * Batch-fetches cuisines for all restaurants to avoid N+1 queries per card.
     *
     * @param \App\Models\Restaurant[] $restaurants
     * @return array<int, \App\Models\CuisineType[]>
     */
    private function buildCuisineMap(array $restaurants): array
    {
        $ids = array_map(fn ($r) => $r->restaurantId, $restaurants);
        return $this->cuisineTypeRepository->findByRestaurantIds($ids);
    }

    /**
     * Groups restaurant images by type and returns file paths.
     *
     * @param \App\Models\RestaurantImage[] $images
     * @return array<string, string[]>
     */
    private function groupImagesByType(array $images): array
    {
        $grouped = [];
        foreach ($images as $image) {
            $grouped[$image->imageType][] = $image->filePath ?? '';
        }
        return $grouped;
    }

    /**
     * Returns per-restaurant time slots and price cards.
     * Restaurant pricing sourced from CMS content; EventSession pricing reserved for bookable events.
     *
     * @return array{timeSlots: array<mixed>, priceCards: array<mixed>}
     */
    private function getRestaurantScheduleData(string $name): array
    {
        return [
            'timeSlots'  => [],
            'priceCards' => [],
        ];
    }
}
