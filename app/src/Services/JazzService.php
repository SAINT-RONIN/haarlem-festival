<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\JazzPageConstants;
use App\Enums\EventTypeId;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IJazzService;
use App\Services\Interfaces\IScheduleService;

/**
 * Service for Jazz page domain payload.
 *
 * This service only composes raw domain/CMS data.
 * ViewModel mapping and UI defaults are handled in the ViewModel layer.
 */
class JazzService implements IJazzService
{
    public function __construct(
        private readonly ICmsService $cmsService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    public function getJazzPageData(): array
    {
        $pageSlug = JazzPageConstants::PAGE_SLUG;

        return [
            'sections' => [
                JazzPageConstants::SECTION_HERO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_HERO,
                ),
                JazzPageConstants::SECTION_GRADIENT => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_GRADIENT,
                ),
                JazzPageConstants::SECTION_INTRO => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_INTRO,
                ),
                JazzPageConstants::SECTION_VENUES => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_VENUES,
                ),
                JazzPageConstants::SECTION_PRICING => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_PRICING,
                ),
                JazzPageConstants::SECTION_SCHEDULE_CTA => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_SCHEDULE_CTA,
                ),
                JazzPageConstants::SECTION_ARTISTS => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_ARTISTS,
                ),
                JazzPageConstants::SECTION_BOOKING_CTA => $this->cmsService->getSectionContent(
                    $pageSlug,
                    JazzPageConstants::SECTION_BOOKING_CTA,
                ),
            ],
            'scheduleSectionData' => $this->scheduleService->getScheduleData(
                JazzPageConstants::PAGE_SLUG,
                EventTypeId::Jazz->value,
                JazzPageConstants::SCHEDULE_MAX_DAYS,
            ),
        ];
    }
}
