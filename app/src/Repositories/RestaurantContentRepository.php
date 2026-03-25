<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\RestaurantContentMapper;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to Restaurant page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to RestaurantContentMapper.
 */
class RestaurantContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the restaurant cards section content. */
    public function findCardsContent(string $pageSlug, string $sectionKey): RestaurantCardsSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapCards($raw);
    }

    /** Fetches the restaurant detail section content. */
    public function findDetailContent(string $pageSlug, string $sectionKey): RestaurantDetailSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapDetail($raw);
    }

    /** Fetches the restaurant intro split section content. */
    public function findIntroContent(string $pageSlug, string $sectionKey): RestaurantIntroSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntro($raw);
    }

    /** Fetches the restaurant intro split 2 section content. */
    public function findIntroSplit2Content(string $pageSlug, string $sectionKey): RestaurantIntroSplit2SectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntroSplit2($raw);
    }

    /** Fetches the restaurant instructions section content. */
    public function findInstructionsContent(string $pageSlug, string $sectionKey): RestaurantInstructionsSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapInstructions($raw);
    }
}
