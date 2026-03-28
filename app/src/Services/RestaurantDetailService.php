<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\RestaurantDetailConstants;
use App\Constants\RestaurantPageConstants;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Models\RestaurantDetailEvent;
use App\Models\RestaurantDetailPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\RestaurantContentRepository;
use App\Services\Interfaces\IRestaurantDetailService;

class RestaurantDetailService implements IRestaurantDetailService
{
    public function __construct(
        private readonly GlobalContentRepository     $globalContentRepo,
        private readonly RestaurantContentRepository $restaurantContentRepo,
        private readonly IEventRepository            $eventRepository,
        private readonly IMediaAssetRepository       $mediaAssetRepository,
    ) {
    }

    /**
     * @throws RestaurantEventNotFoundException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): RestaurantDetailPageData
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $event          = $this->findRestaurantEventBySlug($normalizedSlug);
        $cms            = $this->restaurantContentRepo->findEventCmsData(
            RestaurantDetailConstants::PAGE_SLUG,
            RestaurantDetailConstants::eventSectionKey($event->eventId),
        );

        return new RestaurantDetailPageData(
            event:             $event,
            cms:               $cms,
            sharedCms:         $this->restaurantContentRepo->findDetailContent(
                                   RestaurantPageConstants::PAGE_SLUG,
                                   RestaurantPageConstants::SECTION_DETAIL,
                               ),
            globalUiContent:   $this->globalContentRepo->findGlobalUiContent(
                                   GlobalUiConstants::PAGE_SLUG,
                                   GlobalUiConstants::SECTION_KEY,
                               ),
            featuredImagePath: $this->resolveImagePath($event->featuredImageAssetId),
            timeSlots:         $this->parseTimeSlots($cms->timeSlots),
            priceCards:        $this->buildPriceCards($cms->priceAdult),
        );
    }

    /**
     * @throws RestaurantEventNotFoundException if the slug is empty or contains a path separator
     */
    private function normalizeSlug(string $slug): string
    {
        $normalized = trim(strtolower(rawurldecode($slug)));
        if ($normalized === '' || str_contains($normalized, '/')) {
            throw new RestaurantEventNotFoundException($slug);
        }
        return trim($normalized, '-');
    }

    /**
     * @throws RestaurantEventNotFoundException if no active restaurant event matches the slug
     */
    private function findRestaurantEventBySlug(string $slug): RestaurantDetailEvent
    {
        $event = $this->eventRepository->findActiveRestaurantBySlug($slug);
        if ($event === null) {
            throw new RestaurantEventNotFoundException($slug);
        }
        return $event;
    }

    private function resolveImagePath(?int $assetId): ?string
    {
        if ($assetId === null) {
            return null;
        }
        return $this->mediaAssetRepository->findById($assetId)?->filePath;
    }

    /**
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
     * Builds price cards from the adult price. Under-12 price is always half the adult price.
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
            ['label' => 'Per adult', 'price' => '€ ' . number_format($adult, 2)],
            ['label' => 'Under 12', 'price' => '€ ' . number_format($adult / 2, 2)],
        ];
    }
}
