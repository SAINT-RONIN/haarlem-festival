<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\RestaurantContentMapper;
use App\Content\RestaurantCardsSectionContent;
use App\Content\RestaurantDetailSectionContent;
use App\Content\RestaurantInstructionsSectionContent;
use App\Content\RestaurantIntroSectionContent;
use App\Content\RestaurantIntroSplit2SectionContent;

/**
 * Provides typed access to Restaurant page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to RestaurantContentMapper.
 */
class RestaurantContentRepository extends BaseContentRepository implements Interfaces\IRestaurantContentRepository
{
    /** Fetches the restaurant cards section content. */
    public function findCardsContent(string $pageSlug, string $sectionKey): RestaurantCardsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapCards($raw);
    }

    /** Fetches the restaurant detail section content. */
    public function findDetailContent(string $pageSlug, string $sectionKey): RestaurantDetailSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapDetail($raw);
    }

    /** Fetches the restaurant intro split section content. */
    public function findIntroContent(string $pageSlug, string $sectionKey): RestaurantIntroSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntro($raw);
    }

    /** Fetches the restaurant intro split 2 section content. */
    public function findIntroSplit2Content(string $pageSlug, string $sectionKey): RestaurantIntroSplit2SectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntroSplit2($raw);
    }

    /** Fetches the restaurant instructions section content. */
    public function findInstructionsContent(string $pageSlug, string $sectionKey): RestaurantInstructionsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapInstructions($raw);
    }
}
