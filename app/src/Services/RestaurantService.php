<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailData;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Models\RestaurantPageData;
use App\Repositories\CmsContentRepository;
use App\Repositories\Interfaces\ICuisineTypeRepository;
use App\Repositories\RestaurantImageRepository;
use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant page.
 *
 * Returns typed data models.
 * Mapping to ViewModels happens in RestaurantMapper.
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
        private CmsContentRepository $cmsService,
        private RestaurantRepository $restaurantRepository,
        private RestaurantImageRepository $restaurantImageRepository,
        private ICuisineTypeRepository $cuisineTypeRepository,
    ) {
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
        $restaurants = $this->restaurantRepository->findAllActive();
        $cuisinesByRestaurant = $this->buildCuisineMap($restaurants);

        return new RestaurantPageData(
            heroContent: HeroSectionContent::fromRawArray(
                $this->cmsService->getHeroSectionContent(self::PAGE_SLUG),
            ),
            globalUiContent: GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent('home', 'global_ui'),
            ),
            gradientSection: RestaurantGradientSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_GRADIENT),
            ),
            introSplitSection: RestaurantIntroSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_INTRO_SPLIT),
            ),
            introSplit2Section: RestaurantIntroSplit2SectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_INTRO_SPLIT2),
            ),
            instructionsSection: RestaurantInstructionsSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_INSTRUCTIONS),
            ),
            cardsSection: RestaurantCardsSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_CARDS),
            ),
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
            cms: RestaurantDetailSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_DETAIL),
            ),
            globalUiContent: GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent('home', 'global_ui'),
            ),
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
        $ids = array_map(fn($r) => $r->restaurantId, $restaurants);
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
