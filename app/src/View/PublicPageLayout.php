<?php

declare(strict_types=1);

namespace App\View;

use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * Layout settings for public pages rendered inside the shared shell.
 *
 * Pages that extend BaseViewModel can leave the shell-data fields
 * (currentPage, includeNav, isLoggedIn, globalUi, heroData, gradientSection,
 * introSplitSection) null — _shell.php will fall back to reading them off the
 * viewmodel. Pages without a BaseViewModel must supply these fields here.
 */
final readonly class PublicPageLayout
{
    /**
     * @param list<ViewTemplate> $contentTemplates
     * @param list<string> $extraScripts
     */
    public function __construct(
        public array $contentTemplates = [],
        public bool $includeHero = true,
        public bool $includeEventSections = false,
        public ?string $eventIntroSectionId = null,
        public ?string $eventIntroImageClass = null,
        public string $mainClass = 'w-full bg-sand inline-flex flex-col justify-start items-center',
        public ?string $mainId = null,
        public bool $mainFocusable = false,
        public ?string $currentPage = null,
        public ?bool $includeNav = null,
        public ?bool $isLoggedIn = null,
        public ?GlobalUiData $globalUi = null,
        public ?HeroData $heroData = null,
        public ?GradientSectionData $gradientSection = null,
        public ?IntroSplitSectionData $introSplitSection = null,
        public array $extraScripts = [],
    ) {}
}
