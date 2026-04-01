<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\RestaurantPageConstants;
use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailEvent;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Models\RestaurantEventCmsData;
use App\Models\RestaurantListingData;
use App\Models\RestaurantPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService implements IRestaurantService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
        private readonly IEventRepository      $eventRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    public function getRestaurantPageData(): RestaurantPageData
    {
        $events      = $this->eventRepository->findActiveRestaurantEvents();
        $restaurants = array_map(fn(RestaurantDetailEvent $e) => $this->buildListingData($e), $events);

        $slug = RestaurantPageConstants::PAGE_SLUG;

        return new RestaurantPageData(
            heroContent:         HeroSectionContent::fromRawArray(
                                     $this->cmsContent->getHeroSectionContent($slug),
                                 ),
            globalUiContent:     GlobalUiContent::fromRawArray(
                                     $this->cmsContent->getSectionContent(
                                         GlobalUiConstants::PAGE_SLUG,
                                         GlobalUiConstants::SECTION_KEY,
                                     ),
                                 ),
            gradientSection:     RestaurantGradientSectionContent::fromRawArray(
                                     $this->cmsContent->getSectionContent($slug, RestaurantPageConstants::SECTION_GRADIENT),
                                 ),
            introSplitSection:   RestaurantIntroSectionContent::fromRawArray(
                                     $this->cmsContent->getSectionContent($slug, RestaurantPageConstants::SECTION_INTRO_SPLIT),
                                 ),
            introSplit2Section:  RestaurantIntroSplit2SectionContent::fromRawArray(
                                     $this->cmsContent->getSectionContent($slug, RestaurantPageConstants::SECTION_INTRO_SPLIT2),
                                 ),
            instructionsSection: RestaurantInstructionsSectionContent::fromRawArray(
                                     $this->cmsContent->getSectionContent($slug, RestaurantPageConstants::SECTION_INSTRUCTIONS),
                                 ),
            cardsSection:        RestaurantCardsSectionContent::fromRawArray(
                                     $this->cmsContent->getSectionContent($slug, RestaurantPageConstants::SECTION_CARDS),
                                 ),
            restaurants:         $restaurants,
        );
    }

    private function buildListingData(RestaurantDetailEvent $event): RestaurantListingData
    {
        return new RestaurantListingData(
            event:     $event,
            cms:       RestaurantEventCmsData::fromRawArray(
                           $this->cmsContent->getSectionContent(
                               RestaurantPageConstants::PAGE_SLUG,
                               'restaurant_event_' . $event->eventId,
                           ),
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
