<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\RestaurantDetailConstants;
use App\Constants\RestaurantPageConstants;
use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailEvent;
use App\Models\RestaurantDetailPageData;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantEventCmsData;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Models\RestaurantListingData;
use App\Models\RestaurantPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IRestaurantService;

/**
 * Service for preparing all data needed by the Restaurant pages.
 *
 * Follows the same pattern as StorytellingDetailService:
 *  - IEventRepository  → finds restaurant events by slug or lists all active ones
 *  - ICmsContentRepository → shared page CMS + per-event CMS sections
 *  - IMediaAssetRepository → resolves featured image asset IDs to file paths
 *
 * No IRestaurantRepository: restaurant-specific content (address, chef, menu,
 * pricing, etc.) lives in per-event CMS sections keyed by eventId.
 */
class RestaurantService implements IRestaurantService
{
    public function __construct(
        private ICmsContentRepository $cmsService,
        private IEventRepository      $eventRepository,
        private IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
        $events      = $this->eventRepository->findActiveRestaurantEvents();
        $restaurants = array_map(fn(RestaurantDetailEvent $e) => $this->buildListingData($e), $events);

        return new RestaurantPageData(
            heroContent: HeroSectionContent::fromRawArray(
                $this->cmsService->getHeroSectionContent(RestaurantPageConstants::PAGE_SLUG),
            ),
            globalUiContent: GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent(GlobalUiConstants::PAGE_SLUG, GlobalUiConstants::SECTION_KEY),
            ),
            gradientSection: RestaurantGradientSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_GRADIENT),
            ),
            introSplitSection: RestaurantIntroSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INTRO_SPLIT),
            ),
            introSplit2Section: RestaurantIntroSplit2SectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INTRO_SPLIT2),
            ),
            instructionsSection: RestaurantInstructionsSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_INSTRUCTIONS),
            ),
            cardsSection: RestaurantCardsSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_CARDS),
            ),
            restaurants: $restaurants,
        );
    }

    public function getRestaurantDetailData(string $slug): ?RestaurantDetailPageData
    {
        $slug  = $this->normalizeSlug($slug);
        $event = $this->eventRepository->findActiveRestaurantBySlug($slug);

        if ($event === null) {
            return null;
        }

        $cms = $this->fetchEventCmsData($event->eventId);

        return new RestaurantDetailPageData(
            event:             $event,
            cms:               $cms,
            sharedCms:         RestaurantDetailSectionContent::fromRawArray(
                $this->cmsService->getSectionContent(RestaurantPageConstants::PAGE_SLUG, RestaurantPageConstants::SECTION_DETAIL),
            ),
            globalUiContent:   GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent(GlobalUiConstants::PAGE_SLUG, GlobalUiConstants::SECTION_KEY),
            ),
            featuredImagePath: $this->resolveImagePath($event->featuredImageAssetId),
            timeSlots:         $this->parseTimeSlots($cms->timeSlots),
            priceCards:        $this->buildPriceCards($cms->priceAdult),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildListingData(RestaurantDetailEvent $event): RestaurantListingData
    {
        return new RestaurantListingData(
            event:     $event,
            cms:       $this->fetchEventCmsData($event->eventId),
            imagePath: $this->resolveImagePath($event->featuredImageAssetId),
        );
    }

    private function fetchEventCmsData(int $eventId): RestaurantEventCmsData
    {
        return RestaurantEventCmsData::fromRawArray(
            $this->cmsService->getSectionContent(
                RestaurantDetailConstants::PAGE_SLUG,
                RestaurantDetailConstants::eventSectionKey($eventId),
            ),
        );
    }

    private function resolveImagePath(?int $assetId): ?string
    {
        if ($assetId === null) {
            return null;
        }
        return $this->mediaAssetRepository->findById($assetId)?->filePath;
    }

    private function normalizeSlug(string $slug): string
    {
        return trim(strtolower(rawurldecode($slug)), '-');
    }

    /**
     * Parses a comma-separated time slots string into a clean array.
     *
     * @return string[]
     */
    private function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Builds price cards from the adult price.
     * Under-12 price is always half the adult price.
     *
     * @return array{label: string, price: string}[]
     */
    private function buildPriceCards(?string $priceAdultStr): array
    {
        if ($priceAdultStr === null || $priceAdultStr === '') {
            return [];
        }

        $adult = (float) $priceAdultStr;

        return [
            ['label' => 'Per adult (drinks not included)',   'price' => '€ ' . number_format($adult, 2)],
            ['label' => 'Under 12 (drinks not included)',    'price' => '€ ' . number_format($adult / 2, 2)],
        ];
    }
}
