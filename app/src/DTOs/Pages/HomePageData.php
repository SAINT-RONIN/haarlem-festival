<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Content\GlobalUiContent;
use App\Content\HeroSectionContent;

/**
 * Typed result returned by HomeService::getHomePageData().
 * Carries raw data for the home page — formatting happens in HomeMapper.
 */
final readonly class HomePageData
{
    /**
     * @param array<string, array<string, ?string>> $cmsContent    All CMS sections for the home page
     * @param HomeEventTypeData[]                   $eventTypes    Event type showcase data
     * @param HomeLocationData[]                    $locations     Venue and restaurant map markers
     * @param HomeScheduleDayData[]                 $scheduleDays  Schedule day groups
     */
    public function __construct(
        public array $cmsContent,
        public HeroSectionContent $heroContent,
        public GlobalUiContent $globalUiContent,
        public array $eventTypes,
        public array $locations,
        public array $scheduleDays,
    ) {}
}
