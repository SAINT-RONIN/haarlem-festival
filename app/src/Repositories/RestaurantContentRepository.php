<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantEventCmsData;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to Restaurant page CMS content sections.
 */
class RestaurantContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    public function findGradientContent(string $pageSlug, string $sectionKey): RestaurantGradientSectionContent
    {
        return RestaurantGradientSectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findIntroContent(string $pageSlug, string $sectionKey): RestaurantIntroSectionContent
    {
        return RestaurantIntroSectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findIntroSplit2Content(string $pageSlug, string $sectionKey): RestaurantIntroSplit2SectionContent
    {
        return RestaurantIntroSplit2SectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findInstructionsContent(string $pageSlug, string $sectionKey): RestaurantInstructionsSectionContent
    {
        return RestaurantInstructionsSectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findCardsContent(string $pageSlug, string $sectionKey): RestaurantCardsSectionContent
    {
        return RestaurantCardsSectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findDetailContent(string $pageSlug, string $sectionKey): RestaurantDetailSectionContent
    {
        return RestaurantDetailSectionContent::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }

    public function findEventCmsData(string $pageSlug, string $sectionKey): RestaurantEventCmsData
    {
        return RestaurantEventCmsData::fromRawArray(
            $this->cmsContent->getSectionContent($pageSlug, $sectionKey),
        );
    }
}
