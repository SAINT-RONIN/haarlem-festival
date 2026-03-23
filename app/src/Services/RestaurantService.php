<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Models\Restaurant;
use App\Repositories\CmsContentRepository;
use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant page.
 *
 * Returns plain arrays with raw data.
 * Mapping to ViewModels happens in RestaurantMapper.
 *
 * Uses two data sources:
 *  1. CMS (CmsItem table) — for page copy: titles, descriptions, images
 *  2. Domain (Restaurant table) — for real restaurant business data
 */
class RestaurantService implements IRestaurantService
{

    public function __construct(
        private CmsContentRepository $cmsService,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * Returns all raw data needed to render the restaurant listing page.
     */
    public function getRestaurantPageData(): array
    {
        return [
            'heroContent'         => $this->cmsService->getHeroSectionContent(RestaurantPageConstants::PAGE_SLUG),
            'globalUiContent'     => $this->cmsService->getSectionContent('home', 'global_ui'),
            'gradientSection'     => $this->getCmsSection(RestaurantPageConstants::SECTION_GRADIENT),
            'introSplitSection'   => $this->getCmsSection(RestaurantPageConstants::SECTION_INTRO_SPLIT),
            'introSplit2Section'  => $this->getCmsSection(RestaurantPageConstants::SECTION_INTRO_SPLIT2),
            'instructionsSection' => $this->getCmsSection(RestaurantPageConstants::SECTION_INSTRUCTIONS),
            'cardsSection'        => $this->getCmsSection(RestaurantPageConstants::SECTION_CARDS),
            'restaurants'         => $this->restaurantRepository->findAllActive(),
        ];
    }

    /**
     * Returns all raw data needed to render the restaurant detail page.
     * Returns null if the restaurant is not found.
     */
    public function getRestaurantDetailData(int $id): ?array
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        $cms          = $this->getCmsSection(RestaurantPageConstants::SECTION_DETAIL);
        $scheduleData = $this->getRestaurantScheduleData($restaurant);

        return [
            'restaurant'     => $restaurant,
            'imagesByType'   => [],
            'cms'            => $cms,
            'globalUiContent' => $this->cmsService->getSectionContent('home', 'global_ui'),
            'timeSlots'      => $scheduleData['timeSlots'],
            'priceCards'     => $scheduleData['priceCards'],
        ];
    }

    /**
     * Reads one CMS section for this page.
     */
    private function getCmsSection(string $sectionKey): array
    {
        return $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, $sectionKey);
    }

    /**
     * Returns per-restaurant time slots and price cards.
     * Time slots come from Restaurant.TimeSlots (comma-separated string).
     * Prices come from Restaurant.PriceAdult and Restaurant.PriceChild.
     */
    private function getRestaurantScheduleData(Restaurant $restaurant): array
    {
        $slotsString = $restaurant->timeSlots ?? '';
        $timeSlots   = $slotsString !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $slotsString))))
            : [];

        $priceCards = [];
        if ($restaurant->priceAdult !== null) {
            $priceCards[] = [
                'label' => 'Per adult (drinks not included)',
                'price' => '€ ' . number_format($restaurant->priceAdult, 2),
            ];
        }
        if ($restaurant->priceChild !== null) {
            $priceCards[] = [
                'label' => 'Under 12 (drinks not included)',
                'price' => '€ ' . number_format($restaurant->priceChild, 2),
            ];
        }

        return [
            'timeSlots'  => $timeSlots,
            'priceCards' => $priceCards,
        ];
    }
}
