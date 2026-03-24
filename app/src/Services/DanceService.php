<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\DancePageConstants;
use App\Constants\GlobalUiConstants;
use App\Models\DanceExperienceSectionContent;
use App\Models\DanceGradientSectionContent;
use App\Models\DanceIntroSectionContent;
use App\Models\DancePageData;
use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IDanceService;

final class DanceService implements IDanceService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContentRepository,
    ) {
    }

    public function getDancePageData(): DancePageData
    {
        return new DancePageData(
            heroSection: HeroSectionContent::fromRawArray(
                $this->cmsContentRepository->getHeroSectionContent(DancePageConstants::PAGE_SLUG)
            ),
            gradientSection: DanceGradientSectionContent::fromRawArray(
                $this->cmsContentRepository->getSectionContent(
                    DancePageConstants::PAGE_SLUG,
                    DancePageConstants::SECTION_GRADIENT
                )
            ),
            introSection: DanceIntroSectionContent::fromRawArray(
                $this->cmsContentRepository->getSectionContent(
                    DancePageConstants::PAGE_SLUG,
                    DancePageConstants::SECTION_INTRO
                )
            ),
            experienceSection: DanceExperienceSectionContent::fromRawArray(
                $this->cmsContentRepository->getSectionContent(
                    DancePageConstants::PAGE_SLUG,
                    DancePageConstants::SECTION_EXPERIENCE
                )
            ),
            globalUiContent: GlobalUiContent::fromRawArray(
                $this->cmsContentRepository->getSectionContent(
                    GlobalUiConstants::PAGE_SLUG,
                    GlobalUiConstants::SECTION_KEY
                )
            ),
        );
    }
}