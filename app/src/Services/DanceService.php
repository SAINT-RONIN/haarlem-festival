<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\DancePageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Domain\Pages\DancePageData;
use App\Repositories\Interfaces\IDanceContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Services\Interfaces\IDanceService;

class DanceService extends BaseContentService implements IDanceService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private readonly IDanceContentRepository $danceContentRepo,
        private readonly IEventRepository $eventRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    public function getDancePageData(): DancePageData
    {
        return $this->guardPageLoad(
            fn(): DancePageData => $this->buildPageData(DancePageConstants::PAGE_SLUG),
            'Failed to load the Dance page.',
        );
    }

    private function buildPageData(string $pageSlug): DancePageData
    {
        return new DancePageData(
            heroSection: $this->globalContentRepo->findHeroContent($pageSlug),
            gradientSection: $this->globalContentRepo->findGradientContent($pageSlug, SharedSectionKeys::SECTION_GRADIENT),
            introSection: $this->globalContentRepo->findIntroContent($pageSlug, SharedSectionKeys::SECTION_INTRO),
            headlinersSection: $this->danceContentRepo->findHeadlinersContent($pageSlug, DancePageConstants::SECTION_HEADLINERS),
            artistsSection: $this->danceContentRepo->findArtistsContent($pageSlug, DancePageConstants::SECTION_ARTISTS),
            danceArtists: $this->eventRepository->findDanceArtists(),
            globalUiContent: $this->loadGlobalUi(),
        );
    }
}
