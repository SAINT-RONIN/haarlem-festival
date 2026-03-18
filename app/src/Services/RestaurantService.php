<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsContentRepository;
use App\Repositories\RestaurantImageRepository;
use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant page.
 *
 * Returns plain arrays with raw data.
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

    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    public function __construct(
        private CmsContentRepository $cmsService,
        private RestaurantRepository $restaurantRepository,
        private RestaurantImageRepository $restaurantImageRepository,
    ) {
    }

    /**
     * Returns all raw data needed to render the restaurant listing page.
     */
    public function getRestaurantPageData(): array
    {
        return [
            'heroContent'        => $this->cmsService->getHeroSectionContent(self::PAGE_SLUG),
            'globalUiContent'    => $this->cmsService->getSectionContent('home', 'global_ui'),
            'gradientSection'    => $this->getCmsSection(self::SECTION_GRADIENT),
            'introSplitSection'  => $this->getCmsSection(self::SECTION_INTRO_SPLIT),
            'introSplit2Section' => $this->getCmsSection(self::SECTION_INTRO_SPLIT2),
            'instructionsSection' => $this->getCmsSection(self::SECTION_INSTRUCTIONS),
            'cardsSection'       => $this->getCmsSection(self::SECTION_CARDS),
            'restaurants'        => $this->restaurantRepository->findAllActive(),
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

        $images      = $this->restaurantImageRepository->findByRestaurantId($restaurant->restaurantId);
        $imagesByType = $this->groupImagesByType($images);
        $cms         = $this->getCmsSection(self::SECTION_DETAIL);
        $scheduleData = $this->getRestaurantScheduleData($restaurant->name);

        return [
            'restaurant'     => $restaurant,
            'imagesByType'   => $imagesByType,
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
        return $this->cmsService->getSectionContent(self::PAGE_SLUG, $sectionKey);
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
     */
    private function getRestaurantScheduleData(string $name): array
    {
        return [
            'timeSlots'  => [],
            'priceCards' => [],
        ];
    }
}
