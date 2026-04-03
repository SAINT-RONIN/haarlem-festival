<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the event, CMS content, and pre-resolved fields needed to render a
 * single Restaurant detail or reservation page.
 *
 * sharedCms holds labels (section titles, button text) shared by all restaurants.
 * cms holds the per-restaurant data (address, chef, menu, pricing, etc.).
 *
 * Display formatting (price cards, parsed time slots) is handled by RestaurantMapper.
 */
final readonly class RestaurantDetailPageData
{
    public function __construct(
        public RestaurantDetailEvent          $event,
        public RestaurantEventCmsData         $cms,
        public RestaurantDetailSectionContent $sharedCms,
        public GlobalUiContent                $globalUiContent,
        public ?string                        $featuredImagePath,
    ) {
    }
}