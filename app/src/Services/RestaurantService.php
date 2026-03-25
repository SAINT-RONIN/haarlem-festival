<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Pages\RestaurantDetailData;
use App\DTOs\Pages\RestaurantPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\Interfaces\ICuisineTypeRepository;
use App\Repositories\RestaurantContentRepository;
use App\Repositories\RestaurantImageRepository;
use App\Repositories\RestaurantRepository;
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
class RestaurantService implements IRestaurantService
{
    private const PAGE_SLUG = 'restaurant';

    private const SECTION_GRADIENT     = 'gradient_section';
    private const SECTION_INTRO_SPLIT  = 'intro_split_section';
    private const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    private const SECTION_INSTRUCTIONS = 'instructions_section';
    private const SECTION_CARDS        = 'restaurant_cards_section';
    private const SECTION_DETAIL       = 'detail_section';

    public function __construct(
        private readonly GlobalContentRepository $globalContentRepo,
        private readonly RestaurantContentRepository $restaurantContentRepo,
        private readonly RestaurantRepository $restaurantRepository,
        private readonly RestaurantImageRepository $restaurantImageRepository,
        private readonly ICuisineTypeRepository $cuisineTypeRepository,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
        $restaurants = $this->restaurantRepository->findAllActive();
        $cuisinesByRestaurant = $this->buildCuisineMap($restaurants);

        return new RestaurantPageData(
            heroContent: $this->globalContentRepo->findHeroContent(self::PAGE_SLUG),
            globalUiContent: $this->globalUiLoader->load(),
            gradientSection: $this->globalContentRepo->findGradientContent(self::PAGE_SLUG, self::SECTION_GRADIENT),
            introSplitSection: $this->restaurantContentRepo->findIntroContent(self::PAGE_SLUG, self::SECTION_INTRO_SPLIT),
            introSplit2Section: $this->restaurantContentRepo->findIntroSplit2Content(self::PAGE_SLUG, self::SECTION_INTRO_SPLIT2),
            instructionsSection: $this->restaurantContentRepo->findInstructionsContent(self::PAGE_SLUG, self::SECTION_INSTRUCTIONS),
            cardsSection: $this->restaurantContentRepo->findCardsContent(self::PAGE_SLUG, self::SECTION_CARDS),
            restaurants: $restaurants,
            cuisinesByRestaurant: $cuisinesByRestaurant,
        );
    }

    public function getRestaurantDetailData(int $id): ?RestaurantDetailData
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        $images = $this->restaurantImageRepository->findByRestaurantId($restaurant->restaurantId);
        $scheduleData = $this->getRestaurantScheduleData($restaurant->name);
        $cuisineTypes = $this->cuisineTypeRepository->findByRestaurantId($restaurant->restaurantId);

        return new RestaurantDetailData(
            restaurant: $restaurant,
            imagesByType: $this->groupImagesByType($images),
            cms: $this->restaurantContentRepo->findDetailContent(self::PAGE_SLUG, self::SECTION_DETAIL),
            globalUiContent: $this->globalUiLoader->load(),
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
     * TODO: Replace with EventSession/pricing data from database.
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
