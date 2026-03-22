<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed result returned by HomeService::getHomePageData().
 * Carries raw data arrays for the home page — formatting happens in HomeMapper.
 */
final readonly class HomePageData
{
    /**
     * @param array<string, array<string, ?string>> $cmsContent  All CMS sections for the home page
     * @param array<string, ?string>                $heroContent CMS hero section content
     * @param array<string, ?string>                $globalUiContent CMS global_ui section content
     * @param array<int, array<string, mixed>>      $eventTypes  Event type showcase data
     * @param array<int, array<string, mixed>>      $locations   Venue and restaurant map markers
     * @param array<int, array<string, mixed>>      $scheduleDays Schedule day groups
     */
    public function __construct(
        public array $cmsContent,
        public array $heroContent,
        public array $globalUiContent,
        public array $eventTypes,
        public array $locations,
        public array $scheduleDays,
    ) {}
}
