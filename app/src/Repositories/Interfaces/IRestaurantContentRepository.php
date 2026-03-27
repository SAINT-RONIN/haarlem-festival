<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;

/**
 * Typed access to Restaurant page CMS content sections.
 */
interface IRestaurantContentRepository
{
    /** Fetches the restaurant cards section content. */
    public function findCardsContent(string $pageSlug, string $sectionKey): RestaurantCardsSectionContent;

    /** Fetches the restaurant detail section content. */
    public function findDetailContent(string $pageSlug, string $sectionKey): RestaurantDetailSectionContent;

    /** Fetches the restaurant intro split section content. */
    public function findIntroContent(string $pageSlug, string $sectionKey): RestaurantIntroSectionContent;

    /** Fetches the restaurant intro split 2 section content. */
    public function findIntroSplit2Content(string $pageSlug, string $sectionKey): RestaurantIntroSplit2SectionContent;

    /** Fetches the restaurant instructions section content. */
    public function findInstructionsContent(string $pageSlug, string $sectionKey): RestaurantInstructionsSectionContent;
}
