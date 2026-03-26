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
 * Service for preparing all data needed by the Restaurant listing page.
 *
 * Follows the same pattern as StorytellingService:
 *  - IEventRepository       → lists all active restaurant events
 *  - ICmsContentRepository  → shared page CMS sections + per-event card CMS data
 *  - IMediaAssetRepository  → resolves featured image asset IDs to file paths
 */
class RestaurantService implements IRestaurantService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
        private readonly IEventRepository      $eventRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
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
}
