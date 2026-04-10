<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\RestaurantContentMapper;
use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\DTOs\Cms\RestaurantEventCmsData;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;

class RestaurantContentRepository extends BaseContentRepository implements Interfaces\IRestaurantContentRepository
{
    public function findCardsContent(string $pageSlug, string $sectionKey): RestaurantCardsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapCards($raw);
    }

    public function findDetailContent(string $pageSlug, string $sectionKey): RestaurantDetailSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapDetail($raw);
    }

    public function findIntroContent(string $pageSlug, string $sectionKey): RestaurantIntroSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntro($raw);
    }

    public function findIntroSplit2Content(string $pageSlug, string $sectionKey): RestaurantIntroSplit2SectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapIntroSplit2($raw);
    }

    public function findInstructionsContent(string $pageSlug, string $sectionKey): RestaurantInstructionsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapInstructions($raw);
    }

    // Per-event CMS content for a specific restaurant event.
    public function findEventCmsData(string $pageSlug, string $sectionKey): RestaurantEventCmsData
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return RestaurantContentMapper::mapEventCmsData($raw);
    }
}
