<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the event, CMS content, and pre-resolved fields needed to render a
 * single Restaurant detail or reservation page.
 *
 * Mirrors StorytellingDetailPageData: the service resolves image paths, parses
 * time slots, and derives price cards here so the mapper does pure formatting.
 *
 * sharedCms holds labels (section titles, button text) shared by all restaurants.
 * cms holds the per-restaurant data (address, chef, menu, pricing, etc.).
 */
final readonly class RestaurantDetailPageData
{
    /**
     * @param string[]                              $timeSlots  Parsed from cms.timeSlots
     * @param array{label: string, price: string}[] $priceCards Built from cms.priceAdult (child = adult / 2)
     */
    public function __construct(
        public RestaurantDetailEvent       $event,
        public RestaurantEventCmsData      $cms,
        public RestaurantDetailSectionContent $sharedCms,
        public GlobalUiContent             $globalUiContent,
        public ?string                     $featuredImagePath,
        public array                       $timeSlots,
        public array                       $priceCards,
    ) {
    }
}