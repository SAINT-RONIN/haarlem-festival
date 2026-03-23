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
    ) {
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
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
            restaurants: $this->restaurantRepository->findAllActive(),
        );
    }

    public function getRestaurantDetailData(int $id): ?RestaurantDetailData
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        $scheduleData = $this->getRestaurantScheduleData($restaurant);

        return new RestaurantDetailData(
            restaurant: $restaurant,
            cms: RestaurantDetailSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(self::PAGE_SLUG, self::SECTION_DETAIL),
            ),
            globalUiContent: GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent('home', 'global_ui'),
            ),
            timeSlots: $scheduleData['timeSlots'],
            priceCards: $scheduleData['priceCards'],
            images: $this->restaurantImageRepository->findByRestaurantId($restaurant->restaurantId),
        );
    }

    /**
     * Returns per-restaurant time slots and price cards from the Restaurant row.
     *
     * @return array{timeSlots: string[], priceCards: array<array{label: string, price: string}>}
     */
    private function getRestaurantScheduleData(\App\Models\Restaurant $restaurant): array
    {
        $slotsString = $restaurant->timeSlots ?? '';
        $timeSlots   = $slotsString !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $slotsString))))
            : [];

        $priceCards = [];
        if ($restaurant->priceAdult !== null) {
            $priceCards[] = ['label' => 'Per adult (drinks not included)', 'price' => '€ ' . number_format($restaurant->priceAdult, 2)];
        }
        if ($restaurant->priceChild !== null) {
            $priceCards[] = ['label' => 'Under 12 (drinks not included)', 'price' => '€ ' . number_format($restaurant->priceChild, 2)];
        }

        return ['timeSlots' => $timeSlots, 'priceCards' => $priceCards];
    }
}
